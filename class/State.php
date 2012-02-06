<?php
namespace org\opencomb\userstate ;

class State
{
    private $aTemplates = array(
    	'blog' => array(
    			'title_template'=>'{actor} 发表了新日志' ,
    			'body_template'=>'<strong>{subject}</strong><br />{summary}' ,
    	) ,
    	'album' => array(
    			'title_template'=>'{actor} 上传了新图片至相册 {album}' ,
    			'body_template'=>'<div class="clear">共 {picnum} 张图片</div>' ,
    	) ,
    	'doing' => array(
    			'title_template'=>'{actor}：{subject}' ,
    			'body_template'=>'' ,
    	) ,
    	'thread' => array(
    			'title_template'=>'{actor} 在 {title} 发起了新话题 ' ,
    			'body_template'=>'<b>{subject}</b><br>{summary}' ,
    	) ,
    	'blog_comment' => array(
    			'title_template'=>'{actor} 评论了 {touser} 的日志 {title}' ,
    			'body_template'=>'{summary}' ,
    	) ,
    	'doing_comment' => array(
    			'title_template'=>'{actor} 回复了 {touid} 的心情碎语 {title}' ,
    			'body_template'=>'{summary}' ,
    	) ,
    	'thread_comment' => array(
    			'title_template'=>'{actor} 回复了 {touid} 的话题 {title}' ,
    			'body_template'=>'{summary}' ,
    	) ,
    ) ;
    
    public function getTemplate($system)
    {
        return $this->aTemplates[$system];
    }
    
    public function getTitleTemplate($system)
    {
        return $this->aTemplates[$system]["title_template"];
    }
    
    public function getBodyTemplate($system)
    {
        return $this->aTemplates[$system]["body_template"];
    }
	
    public function getStateHtml($type,$o)
    {
        if($o->title_template)
        {
            if($type == "title")
            {
                $sTemplate = $o->title_template;
                $aParams = json_decode($o->title_data,true);
            }
            if($type == "body")
            {
                $sTemplate = $o->body_template;
                $aParams = json_decode($o->body_data,true);
            }
            
            preg_match_all("/\{(.*?)\}/", $sTemplate, $aTemplate);
            
            for($i = 0; $i < sizeof($aTemplate[0]); $i++){
                $aData[] =  $aParams[$aTemplate[1][$i]];
            }
            
        }else{
            $sTemplate = $this->aTemplates[$o->system][$type."_template"];
            
            preg_match_all("/\{(.*?)\}/", $sTemplate, $aTemplate);
            
            $aData_tmp["actor"] = $o->child("info")->nickname; 
            $aData_tmp["picnum"] = $o->picnum; 
            $aData_tmp["subject"] = $o->subject; 
            $aData_tmp["summary"] = $o->summary; 
            $aData_tmp["title"] = $o->title; 
            $aData_tmp["touid"] = $o->child("toinfo")->nickname; 
            
            
            for($i = 0; $i < sizeof($aTemplate[0]); $i++){
                $aData[] =  $aData_tmp[$aTemplate[1][$i]];
            }
        }

        if($aTemplate[0] && $aData)
        {
            $html = str_replace($aTemplate[0], $aData,$sTemplate);
        }else{
            $html = "";
        }
        

        return $html;
    }
}

?>