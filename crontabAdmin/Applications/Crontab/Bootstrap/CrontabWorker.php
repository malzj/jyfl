<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * CrontabWorker for php
 * @author shuiguang
 * @link https://github.com/shuiguang/windows-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Bootstrap;
use Workerman\Worker;
use Workerman\Lib\Timer;
use Crontab\Config;

class CrontabWorker extends Worker
{
    /**
     * 版本号
     * @var string
     */
    const VERSION = '1.0.0';
    
    /**
     * remove the username in crontab command
     * 移除crontab语句中可能存在的用户名,如root,Admintrator
     * @var array
     */
    protected static $current_user = array();
    
    /**
     * cache for each crontab group
     * 定时任务组命令缓存,当filemtime发生变化时将重新缓存,避免频繁读取磁盘
     * @var array
     */
    private static $cron_cache = array();
    
    /**
     * crontab time syntax
     * 定时任务命令时间语法,默认和linux一致,可选Y-m-d H:i:s启用秒级定时功能(需要为每条定时任务添加秒参数,例如* * * * * *表示每1秒执行一次)
     * @var string
     */
    private static $cron_standard = 'Y-m-d H:i';

    /**
     * crontab time syntax
     * linux系统的crontab任务永远在第1秒执行,且添加定时任务后的1分钟之内是不会执行该任务(即使语法上完全满足)
     * 使用该参数$cron_second===false时,将新增定时任务组或定时任务组重启后立即生效(仅当$cron_standard为Y-m-d H:i时有效)
     * 可选0-59(第n秒执行,例如任务组设定在* * * * *执行,$cron_second===6; 00:00:06-00:00:59之间任意秒启动任务,将不会执行,延至下一分钟)
     * 0和false有区别(第0秒执行,例如任务设定在* * * * *执行,$cron_second===0; 00:00:00-00:00:59之间任意秒启动任务,将不会执行,延至下一分钟)
     * 默认值为false(任意秒执行,例如任务设定在* * * * *执行,$cron_second===false; 00:00:00-00:00:59之间任意秒启动任务,将会立刻执行)
     * @var mixed
     */
    private static $cron_second = false;

    /**
     * file scanner interval
     * 定时任务文件扫描间隔值介于0-60之间,保证在$interval内扫描次数大于等于2次,可根据CPU运转压力适当调整,建议值为0.9
     * 分级定时功能$cron_second===false时,在该值在0-60之间(不包含边界),例如59.999999
     * 秒级定时功能或分级定时功能设置了$cron_second指定秒执行,该值在0-1之间(不包含边界),例如0.999999
     * @var float
     */
    private static $interval = 0.9;
    
    /**
     * workerman method
     * 构造方法,系统方法
     * @param string $socket_name
     * @param array $context_option
     * @return null
     */
    public function __construct($socket_name = '', $context_option = array())
    {
        parent::__construct($socket_name, $context_option);
    }

    /**
     * see Workerman.Worker::run()
     * 运行,系统方法
     * @return null
     */
    public function run()
    {
        $this->onWorkerStart = array($this, 'onWorkerStart');
        parent::run();
    }
    
    /**
     * initialize directory
     * 初始化程序所需目录,清除所有过期文件
     * @return null
     */
    public function onWorkerStart()
    {
        $sys_dir = array(
            Config::$cron_dir, 
            Config::$run_dir, 
            Config::$pid_dir, 
            Config::$lock_dir, 
            Config::$forbidden_dir, 
            Config::$log_dir,
        );
        foreach($sys_dir as $dir)
        {
            if(!is_dir($dir))
            {
                mkdir($dir);
            }
        }
        foreach(glob(Config::$pid_dir.'/*.pid') as $cur_file)
        {
            @unlink($cur_file);
        }
        //保存当前脚本的用户,并尝试移除定时任务命令中该用户名
        self::$current_user = array_merge(Config::$exec_user, array(get_current_user()));
        //设置时区
        date_default_timezone_set('PRC');
        //添加扫描器
        Timer::add(self::$interval, array($this, 'startCrontab'));
    }
    
    /**
     * start scaner and analyse crontab task
     * 启动扫描器并分析定时任务
     * @return null
     */
    public function startCrontab()
    {
        //即将执行的任务
        $mission = array();
        //扫描任务列表
        foreach(glob(Config::$cron_dir.'/*'.Config::$cron_suffix) as $cur_file)
        {
            $file = basename($cur_file);
            $run_file = Config::$run_dir.'/'.$file;
            $forbidden_file = Config::$forbidden_dir.'/'.$file;
            //判断是否添加到运行目录同时还要检测任务是否添加到禁止目录
            if(file_exists($run_file) && !file_exists($forbidden_file))
            {
                //仅当定时任务文件和运行状态文件的filemtime完全相同时才会从缓存取值
                if(filemtime($cur_file) == filemtime($run_file))
                {
                    if(isset(self::$cron_cache[$file]['cmd']))
                    {
                        //已命中缓存,从缓存中去读
                        $command_arr = self::$cron_cache[$file]['cmd'];
                    }else{
                        //初次读取文件并缓存到变量中
                        $command_arr = explode("\n", file_get_contents($cur_file));
                        self::$cron_cache[$file]['cmd'] = $command_arr;
                    }
                }else{
                    //未命中缓存,文件更新时间不一致导致变量缓存失效,建议使用手动复制或者rsync能够同步文件修改时间的方式保证filemtime值相同
                    if(strpos($file, Config::$auto_prefix) === 0)
                    {
                        //例外,对断点任务不使用同步方式,以保证断点任务能够从断点处继续执行下去
                        $command_arr = explode("\n", file_get_contents($run_file));
                        //清空断点任务列表,不使用缓存
                        self::$cron_cache[$file]['task'] = array();
                    }else{
                        //普通任务同步文件,包括文件的filemtime
                        $this->sync_file($cur_file, $run_file);
                        $command_arr = explode("\n", file_get_contents($cur_file));
                    }
                    //将当前任务组下所有命令加入缓存
                    self::$cron_cache[$file]['cmd'] = $command_arr;
                }
                $line = 0;
                $start_time = time();
                //去除前导零
                $start_second = intval(ltrim(date('s', $start_time),'0'));
                foreach($command_arr as $command)
                {
                    $line++;
                    //使用#作为注释符
                    if(empty($command) || $command[0] == '#' || $command[0] == 'SHELL' || $command[0] == 'PATH' || $command[0] == 'MAILTO' || $command[0] == 'HOME')
                    {
                        continue;
                    }
                    $command = trim($command);
                    //分割定时时间和命令
                    $part = explode(' ', $command);
                    if(isset($part[5]))
                    {
                        if(self::$cron_standard == 'Y-m-d H:i:s')
                        {
                            $mission['cron_time'] = $part[0].' '.$part[1].' '.$part[2].' '.$part[3].' '.$part[4].' '.$part[5];
                        }else{
                            $mission['cron_time'] = $part[0].' '.$part[1].' '.$part[2].' '.$part[3].' '.$part[4];
                        }
                        $mission['value'] = str_replace($mission['cron_time'], '', $command);
                        //尝试去掉可能存在的执行用户名
                        foreach(self::$current_user as $user)
                        {
                            $mission['value'] = trim(preg_replace('/^\s+'.$user.'\s+/', '', ' '.$mission['value'].' '));
                        }
                        //解析返回执行的秒组成的数组
                        $array = \Lib\ParseCrontab::parse($mission['cron_time'], $start_time);
                        if(is_array($array))
                        {
                            //普通任务使用$cron_standard判断保证每个单位时间内只被执行一次
                            $mission['exec_time'] = date(self::$cron_standard, $start_time);
                            $pid_file = $this->get_pid_file($file, $mission['value']);
                            //例外,断点任务时间需设置为* * * * *,不做单位时间判断,而是使用lock文件保证每个url只被请求一次
                            if(strpos($file, Config::$auto_prefix) === 0)
                            {
                                self::$cron_cache[$file]['task'][$pid_file] = $mission;
                                $this->runCrontab($mission['value'], $file, $line);
                            }else{
                                //普通任务根据待执行的秒数进行执行,兼容linux的crontab的语句只会在第1秒时执行
                                if(self::$cron_standard == 'Y-m-d H:i:s')
                                {
                                    if(in_array($start_second, $array, true))
                                    {
                                        //如果缓存中存在待执行的任务命令信息数组(单条命令语句以pid脚本存储在pid_dir中)
                                        if(isset(self::$cron_cache[$file]['task'][$pid_file]))
                                        {
                                            if(self::$cron_cache[$file]['task'][$pid_file]['exec_time'] == $mission['exec_time'])
                                            {
                                                //已经有命令在执行,当前扫描跳过执行
                                                continue;
                                            }else
                                            {
                                                //未发现任何执行,将当前命令信息数组保存到缓存中
                                                self::$cron_cache[$file]['task'][$pid_file] = $mission;
                                                //正式执行
                                                $this->runCrontab($mission['value'], $file, $line);
                                            }
                                        }else{
                                            //首次执行写入缓存变量
                                            self::$cron_cache[$file]['task'][$pid_file] = $mission;
                                            $this->runCrontab($mission['value'], $file, $line);
                                        }
                                    }
                                }else{
                                    //仅当self::$cron_second===false或者$start_second === self::$cron_second时才正式执行
                                    if(self::$cron_second === false || $start_second === self::$cron_second)
                                    {
                                        //排他处理,保证$this->cron_standard单位时间内以单例模式运行
                                        $mission['exec_time'] = date(self::$cron_standard, time());
                                        if(isset(self::$cron_cache[$file]['task'][$pid_file]) && self::$cron_cache[$file]['task'][$pid_file]['exec_time'] == $mission['exec_time'])
                                        {
                                            //已经有命令在执行,当前扫描跳过执行
                                            continue;
                                        }else
                                        {
                                            //未发现任何执行,将当前命令信息数组保存到缓存中
                                            self::$cron_cache[$file]['task'][$pid_file] = $mission;
                                            //正式执行
                                            $this->runCrontab($mission['value'], $file, $line);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                //定时任务组被停止,被禁止或被删除,从内存中清除
                if(isset(self::$cron_cache[$file]))
                {
                    unset(self::$cron_cache[$file]);
                }
            }
        }
    }
        
    /**
     * 保存文件的filemtime信息,增加缓存命中率
     * @param string $from
     * @param string $to
     * @return null
     */
    public function sync_file($from, $to)
    {
        if(strtoupper(substr(PHP_OS,0,3)) == 'WIN')
        {
            //windows下无法copy filemtime属性,建议手动复制到run_dir目录或使用rsync
            copy($from, $to);
        }else
        {
            //linux下支持-p参数保留filemtime属性
            exec("cp -p $from $to");
        }
    }
     
    /**
     * 获取定时任务组命令的pid文件名
     * @param string $group
     * @param string $value
     * @return null
     */
    public function get_pid_file($group, $value)
    {
        //$group为定时任务组的文件名
        return Config::$pid_dir.'/'.$group.'.'.md5($value).'.'.Config::$pid_suffix;
    }
    
    /**
     * 获取定时任务组命令的lock文件名
     * @param string $group
     * @param string $value
     * @return null
     */
    public function get_lock_file($group, $value)
    {
        //$group为定时任务组的文件名不含后缀,命名不能含有.Config::$cron_suffix字符
        return Config::$lock_dir.'/'.$group.'.'.md5($value).'.'.Config::$lock_suffix;
    }

    /**
     * 异步执行命令,支持重定向,原理为php的exec函数后台执行
     * @param string $value
     * @param string $file
     * @param integer $line
     * @return null
     */
    public function runCrontab($value, $file='', $line='')
    {
        $lock_file = $this->get_lock_file($file, $value);
        if(file_exists($lock_file))
        {
            //例外,当外部程序根据lock文件名规则写入锁文件时将跳过执行
        }else{
            file_put_contents(Config::$log_dir.'/'.date('Ymd').'.log', '<font color="green">['.date('Y-m-d H:i:s').']正在执行'.$value.'['.$file.':'.$line.']</font>'.PHP_EOL, FILE_APPEND|LOCK_EX);
            //构造一个php脚本作为容器后台执行
            $pid_file = $this->get_pid_file($file, $value);
            $contents = '<?php
$value =
<<<EOF
'.$value.'
EOF;
exec($value);';
            //清除文件缓存
            clearstatcache();
            //仅当pid文件不存在的时候重新写入
            if(!file_exists($pid_file))
            {
                file_put_contents($pid_file, $contents);
            }
            //windows和linux下后台执行方式
            if(strtoupper(substr(PHP_OS,0,3)) == 'WIN')
            {
                pclose(popen('start /B '.Config::$exec_path.' '.$pid_file, 'r'));
            }else
            {
                pclose(popen(Config::$exec_path.' '.$pid_file.' > /dev/null &', 'r'));
            }
        }
    }
}
