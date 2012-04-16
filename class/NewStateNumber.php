<?php
namespace org\opencomb\userstate ;

use org\jecat\framework\bean\BeanFactory;

use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class NewStateNumber extends Controller
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
	                    ) ,
	                    'list'=>true,
	            ) ,
	    
	    ) ;
	    
	    
	    if( $this->params["channel"] == "friends")
	    {
	        $aId = $this->requireLogined() ;
	        $aOrm['model:state'] = array(
	                'class' => 'model' ,
	                'orm' => array(
	                        'table' => 'userstate:state' ,
	                        'hasOne:subscription'=>array(    //一对多
	                                'keys'=>array('from','to') ,
	                                'fromkeys'=>'uid',
	                                'tokeys'=>'to',
	                                'table'=>'friends:subscription',
	                        ),
	                ) ,
	                'list'=>true,
	        );
	        
	        $aUid = array();
	        foreach (IdManager::singleton()->iterator() as $v){
	            $aUid[] = $v->userId();
	        }
	        if(count($aUid) > 1)
	        {
	            $sSql = '';
	            foreach($aUid as $nKey => $nUid)
	            {
	                if($nKey)
	                {
	                    $sSql .= ',';
	                }
	                $sSql.='@'.($nKey+1);
	            }
	            $aUid[] =  $aId->userId();
	            $aOrm['model:state']['orm']['where'] = array( "subscription.from in ( {$sSql} ) or uid = @" . count($aUid)+1 , $aUid );
	        }else{
	            $aOrm['model:state']['orm']['where'] = array( 'subscription.from = @1 or uid = @2' , $aId->userId() , $aId->userId() );
	        }
	    }
	    
	    
	    if( $this->params["channel"] == "wownei")
	    {
	        //$aId = $this->requireLogined() ;
	        $aOrm['model:state'] = array(
	                'class' => 'model' ,
	                'orm' => array(
	                        'table' => 'userstate:state' ,
	                        'hasOne:subscription'=>array(    //一对多
	                                'keys'=>array('from','to') ,
	                                'fromkeys'=>'uid',
	                                'tokeys'=>'to',
	                                'table'=>'friends:subscription',
	                        ),
	                        'where' => array( 'stid not like @1',"pull|%") ,
	                ) ,
	                'list'=>true,
	        );
	    
	    }
	    
	    return  $aOrm;
	}
	
	public function process()
	{
        $this->state->prototype()->criteria()->addOrderBy('time',true);
        $this->state->prototype()->criteria()->where()->gt('time',$this->params['time']);
        $this->state->prototype()->criteria()->setLimit(1000);
        
	    $this->state->load() ;
	    echo $this->state->childrenCount();exit;
	    
	    /**
	     * 打印model
	     * $this->state->printStruct();
	     */
	}
}