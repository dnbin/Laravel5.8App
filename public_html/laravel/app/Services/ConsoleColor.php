<?php
/**
 * Created by PhpStorm.
 * User: Vova
 * Date: 05.02.2017
 * Time: 13:30
 */

namespace App\Services;

use Illuminate\Support\Facades\Log;
use JakubOnderka\PhpConsoleColor\ConsoleColor as JakubConsoleColor;

class ConsoleColor extends JakubConsoleColor
{
    protected $line_ending=PHP_EOL;
    protected $console;
    protected $logger;

    /**
     * ConsoleColor constructor.
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @param Log $log
     */
    public function setLogger(Log $log){
        $this->logger=$log;
    }

    /**
     * @param string $msg
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    public function info(string $msg){
        $this->output('green',$msg);
        if(!empty($this->logger)){
            $this->logger::info($msg);
        }
    }

    /**
     * @param string $msg
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    public function warning(string $msg){
        $this->output('yellow',$msg);
        if(!empty($this->logger)){
            $this->logger::warning($msg);
        }
    }

    /**
     * @param string $msg
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    public function error(string $msg){
        $this->output('red',$msg);
        if(!empty($this->logger)){
            $this->logger::error($msg);
        }

    }
    public function plain(string $msg){
        echo $msg.$this->line_ending;
        if(!empty($this->logger)){
            $this->logger::debug($msg);
        }
    }

    /**
     * @param string $color
     * @param string $msg
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    protected function output(string $color,string $msg){
        echo $this->apply($color,$msg).$this->line_ending;
    }

    public function setLineEnding(string $line_ending){
        $this->line_ending=$line_ending;
    }
}