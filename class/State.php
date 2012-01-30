<?php
namespace org\opencomb\userstate ;

class State
{
    private $aTemplates = array(
    	'blog' => array(
    			'title_template'=>"title_template" ,
    			'body_template'=>"body_template" ,
    	) ,
    	'album' => array(
    			'title_template'=>"title_template" ,
    			'body_template'=>"body_template" ,
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
	
}

?>