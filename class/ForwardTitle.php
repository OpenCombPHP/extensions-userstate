<?php
namespace org\opencomb\userstate ;

use org\opencomb\coresystem\mvc\controller\UserSpace;

use com\wonei\woneibridge\aspect\NamecardAspect;
use org\jecat\framework\util\String;
use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\db\DB;
use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller;

class ForwardTitle extends UserSpace
{
	public function createBeanConfig()
	{
        
	    $aOrm = array(
	    		
    		/**
    		 * 模型
    		 * list = true 返回多条记录
    		 */
            'model:state' => array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'userstate:state' ,
                        'columns' => array("stid","system","forwardtid","replytid","uid","title","body","article_title","article_uid","time","data","client") ,     
            			'hasMany:astate' => array(    //一对多
                            'columns' => array("service","sid","pullcommenttime","old_comment_page") ,   
            				'table' => 'oauth:state',
            				'fromkeys'=>'stid',
            				'tokeys'=>'stid',
        		            'keys'=>array('service','sid'),
            			) ,
                		'hasOne:info' => array(
                			'table' => 'coresystem:userinfo' ,
            				'fromkeys'=>'uid',
            				'tokeys'=>'uid',
                		) ,
            		) ,
            ) ,
	            
		) ;
	    return  $aOrm;
	}
	
	public function process()
	{
	    $sSql[] = "stid=@1";
	    $arrParamsForSql['@1'] = $this->params()->stid;
	    
	    $this->state->loadSql(implode(" and ", $sSql),$arrParamsForSql) ;
	    $sTmp = $this->state->forwardtid;
	    if(!empty($sTmp))
	    {
	        echo '||@'.$this->state->child('info')->nickname.':'.$this->state->title;
	    }
	    exit;
	}
}
