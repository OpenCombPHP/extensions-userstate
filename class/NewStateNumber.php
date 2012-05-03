<?php
namespace org\opencomb\userstate ;

use org\jecat\framework\db\DB;

use org\jecat\framework\bean\BeanFactory;

use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class NewStateNumber extends Controller
{
	public function createBeanConfig()
	{
	    
	    $aOrm = array(
	    
	            'title' => '动态墙' ,
	    
	            /**
	             * 模型
	    * list = true 返回多条记录
	    */
	            'model:state' => array(
	                    'class' => 'model' ,
	                    'orm' => array(
	                            'table' => 'userstate:state' ,
	                            'columns' => array("stid","system","forwardtid","replytid","uid","title","body","article_title","article_uid","time","data","client") ,
	                            'forceIndex'=>'time-stid' ,
	                            'orderDesc' => 'time' ,
	                    ) ,
	                    'list'=>true,
	            ) ,
	    
	    ) ;
	    
	    // 频道
	    if( $this->params["channel"] == "friends")
	    {
	        $aId = $this->requireLogined() ;
	    
	        $aOrm['model:state'] = array(
	                'class' => 'model' ,
	                'orm' => array(
	                        'table' => 'userstate:state' ,
	                        'columns' => array("stid","system","forwardtid","replytid","uid","title","body","article_title","article_uid","time","data","client") ,
	                        'hasOne:subscription'=>array(    //一对多
	                                'columns' => array("from","to") ,
	                                'keys'=>array('from','to') ,
	                                'fromkeys'=>'uid',
	                                'tokeys'=>'to',
	                                'table'=>'friends:subscription',
	                        ),
	                        'groupby'=>'stid',
	                        'orderDesc' => 'time' ,
	                ) ,
	                'list'=>true,
	        );
	    
	        $aUid = array();
	        foreach (IdManager::singleton()->iterator() as $v){
	            $aUid["@".$v->userId()] = $v->userId();
	        }
	        if(count($aUid) > 1)
	        {
	            $sSql = array();
	            foreach($aUid as $nKey => $nUid)
	            {
	                $sSql[] ='@'.$nUid;
	            }
	            $aUid['@me'] =  $nUid;
	            $aOrm['model:state']['orm']['where'] = array( "(subscription.from in ( ".implode(',',$sSql)." ) or uid = @me )", $aUid );
	        }else{
	            $aOrm['model:state']['orm']['where'] = array( '(subscription.from = @meuid1 or uid = @meuid2)' , array("@meuid1"=>$aId->userId() , "@meuid2"=>$aId->userId()) );
	        }
	    }
	    
	    
	    // 所属网站
	    if($this->params["service"])
	    {
	        if($this->params["service"] == "wownei.com"){
	            if(empty($aOrm['model:state']['orm']['where']))
	            {
	                $aOrm['model:state']['orm']['where'] = array("stid not like 'pull|%'");
	            }else{
	                $aWhere[0] = $aOrm['model:state']['orm']['where'][0]." and stid not like 'pull|%'";
	                $aWhere[1] = $aOrm['model:state']['orm']['where'][1];
	                $aOrm['model:state']['orm']['where'] = array($aWhere[0],$aWhere[1]) ;
	            }
	    
	        }else{
	            // 增加一个用于查询条件的表
	            $aOrm['model:state']['orm']['hasOne:sorce'] = array(    //一对一
	                    'columns' => array() ,
	                    'table' => 'oauth:state',
	                    'fromkeys'=>'stid',
	                    'tokeys'=>'stid',
	                    'keys'=>array('service','sid'),
	            ) ;
	            // 增加条件
	            if(empty($aOrm['model:state']['orm']['where']))
	            {
	                $aOrm['model:state']['orm']['where'] = array('sorce.service = @service',array('@service'=>$this->params["service"])) ;
	            }else
	            {
	                $aWhere[0] = $aOrm['model:state']['orm']['where'][0].' and sorce.service = @service';
	                $aWhere[1] = $aOrm['model:state']['orm']['where'][1];
	                $aWhere[1]['@service'] = $this->params["service"];
	                $aOrm['model:state']['orm']['where'] = array($aWhere[0],$aWhere[1]) ;
	            }
	    
	        }
	    }
	    return  $aOrm;
	}
	
	public function process()
	{
	    $oState = new State();
	    $sSql = array();
	    $arrParamsForSql = array();
	    
	    if($this->params["system"])
	    {
	        $sSql[] = 'system = @' . (count($sSql)+1);
	        $arrParamsForSql[] = $this->params["system"];
	    }
	    
	    if($this->params["sex"])
	    {
	        $sSql[] = 'info.sex = @' . (count($sSql)+1);
	        $arrParamsForSql[] = $this->params["sex"];
	    }
        $sSql[] = 'time > @' . (count($sSql)+1);
        $arrParamsForSql[] = $this->params['time'];
        
        $this->state->setPagination(100,1);
        
	    $this->state->loadSql(implode(" and ", $sSql),$arrParamsForSql) ;
	    echo $this->state->childrenCount();exit;
	    /**
	     * 打印model
	     * $this->state->printStruct();
	     */
	}
}