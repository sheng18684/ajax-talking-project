<?php

exit;
set_time_limit(0);
 /**
 * 多线程下载抓取
 */
class catchImg extends Thread{
    public $url;  
    public $data;
    public function __construct($url){
        $this->url=$url;
    }

    public function run(){
        if(($url = $this->url))  
        {  
            $this->data = model_http_curl_get($url);
            // echo $url."->ID:".Thread::getCurrentThreadId()."<br />";
        } 
    }
}

function model_thread_result_get($urls_array,$loadPath)   
  {  
      header("Content-Type: text/html; charset=utf-8"); 

      foreach ($urls_array as $key => $value)   
      {  
          $thread_array[$key] = new catchImg($value["url"]);  

          $thread_array[$key]->start(); 
          echo "<font color='red'>".microtime(true)."---主线程ID".(Thread::getCurrentThreadId())."</font><br/>";
      }  
      $urldetail=array();
      foreach ($thread_array as $thread_array_key => $thread_array_value)   
      {  
        while($thread_array[$thread_array_key]->isRunning())  
        {  
          usleep(10);  
        }  
        if($thread_array[$thread_array_key]->join())  
        {  
          preg_match_all("#<a href=\"(.*?)\".*?class=\"s xst\">#",$thread_array[$thread_array_key]->data,$detailSrc);  
          $urldetail=array_merge($urldetail, $detailSrc[1]);
        }
        
      }
      // $urldetail=array();
      // foreach ($urls_array as $value) {
      //   $page_content=model_http_curl_get($value['url']);
      //   preg_match_all("#<a href=\"(.*?)\".*?class=\"s xst\">#",$page_content,$detailSrc);
      //   $urldetail=array_merge($urldetail, $detailSrc[1]);
      // }
      $count=count($thread_array)+1;
      if($urldetail){
        foreach ($urldetail as $key => $value) {
          $detail_url="http://pic.yesky.com/bbs/".$value;

          /*多线程*/
          $detail_array[$key+1] = new catchImg($detail_url);
          $detail_array[$key+1]->start();
          echo "<font>".microtime(true)."---主线程ID".(Thread::getCurrentThreadId())."抓取url：".$detail_url."</font><br/>";
        }

        foreach($detail_array as $detail_array_key=>$detail_array_value){
          while($detail_array[$detail_array_key]->isRunning()){
            usleep(10);
          }
          if($detail_array[$detail_array_key]->join()){
            preg_match_all("#<img.*?class=\"zoom\" file=\"(.*?)\"#",$detail_array[$detail_array_key]->data,$imgSrc);
            if($imgSrc[1]){
              foreach($imgSrc[1] as $imgSrcValue){

                download_image($imgSrcValue, $fileName = '', $loadPath);
              }
            }
            
          }
        }
      }  
      // return $variable_data;  
  }

  function model_http_curl_get($url,$userAgent="")   
  {  
      $userAgent = $userAgent ? $userAgent : 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2)';   
      $curl = curl_init();  
      curl_setopt($curl, CURLOPT_URL, $url);  
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
      curl_setopt($curl, CURLOPT_TIMEOUT, 5);  
      curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);  
      $result = curl_exec($curl);  
      curl_close($curl);  
      return $result;  
  }

  /**
 * 下载远程图片到本地
 *
 * @param string $url 远程文件地址
 * @param string $filename 保存后的文件名（为空时则为随机生成的文件名，否则为原文件名）
 * @param array $fileType 允许的文件类型
 * @param string $dirName 文件保存的路径（路径其余部分根据时间系统自动生成）
 * @param int $type 远程获取文件的方式
 * @return json 返回文件名、文件的保存路径
 * @author blog.snsgou.com
 */
function download_image($url, $fileName = '', $dirName, $fileType = array('jpg', 'gif', 'png','jpeg','bmp','png'), $type = 1)
{
    if ($url == '')
    {
        return false;
    }

    
    // 获取文件原文件名
    $defaultFileName = basename($url);
 
    // 获取文件类型
    $suffix = substr(strrchr($url, '.'), 1);
    if (!in_array($suffix, $fileType))
    {
        return false;
    }
 
    // 设置保存后的文件名
    $fileName = $fileName == '' ? time() . rand(0, 9) . '.' . $suffix : $defaultFileName;
 
    // 获取远程文件资源
    if ($type)
    {
        $ch = curl_init();
        $timeout = 86400;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file = curl_exec($ch);
        curl_close($ch);
    }
    else
    {
        ob_start();
        readfile($url);
        $file = ob_get_contents();
        ob_end_clean();
    }
 
    // 设置文件保存路径
    //$dirName = $dirName . '/' . date('Y', time()) . '/' . date('m', time()) . '/' . date('d', time());
    $dirName = $dirName . '/' . date('Ym', time());
    if (!file_exists($dirName))
    {
        mkdir($dirName, 0777, true);
    }
 
    // 保存文件
    $res = fopen($dirName . '/' . $fileName, 'a');
    fwrite($res, $file);
    fclose($res);
    
    // return array(
    //     'fileName' => $fileName,
    //     'saveDir' => $dirName
    // );
}
  
  for ($i=1; $i < 15; $i++)   
  {   
      $urls_array[] = array("name" => "4K论坛", "url" => "http://pic.yesky.com/bbs/forum-61111-".$i.".html");  
  }  
  $loadPath="E:/images/test";
  $t = microtime(true);  
  $result = model_thread_result_get($urls_array,$loadPath);  
  $e = microtime(true);  
  echo "多线程：".($e-$t)."\n";