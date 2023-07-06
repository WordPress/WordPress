<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

/**
 * WordPress cron substitute
 *
 * @author realmag777
 * @site https://pluginus.net
 */
final class PN_WP_CRON_WOOF_TURBO_MODE {

    public $actions = array();
    public $cron_key = null;
    public $hook="";
    public $filter="";
    public $step=10;
    public function __construct($key,$hook,$filter)
    {
        $this->cron_key = $key;
        $this->hook=$hook;
        $this->filter=$filter;
        $this->actions = get_option($this->cron_key, array());
    }

    public function process()
    {
		$last_update = get_option("woof_cron_limit_do",0);
		if((time() - $last_update)<5) {
			return;
		}
        if (!empty($this->actions))
        {
            $now = time();
            foreach ($this->actions as $id => $event)
            {
				if (!is_array($event)) {
					continue;
				}
                if ($event['next'] <= $now)
                {
                    if(empty($event['ids']) AND $event['count']==0){
                    
                        $event['ids']=apply_filters($this->filter,$event['ids'],$id);

                    }
                    if(count($event['ids'])>$event['count']){
                       $ids=array();
                       $ids= array_slice($event['ids'], $event['count'],$this->step);
                       $event['count']+=$this->step;
                       $this->actions[$id] = $event; 
                       $this->update();
					   
                       do_action($this->hook,$id,$ids,false);
					   
                    }else{

                        $event['next'] = $now + $event['recurrence'];
                        $event['count']=0;
                        $event['ids']=array();
                        $this->actions[$id] = $event;
                        $this->update(); 
                        do_action($this->hook,$id,array(),true);

                    }
					update_option("woof_cron_limit_do",time());
                }
            }
        }
    }

    public function attach($id, $start_time, $recurrence)
    {
        //recurrence - in seconds
        $next = $start_time + $recurrence;
        $this->actions[$id] = array(
            'start_time' => $start_time,
            'next' => $next,
            'recurrence' => $recurrence,
            'count'=>0,
            'ids'=>array(),
                
        );
        $this->update();
    }

    public function is_attached($id, $recurrence = 0)
    {

        if (isset($this->actions[$id]) AND (int)$recurrence !== 0)
        {
            if ((int) $this->actions[$id]['recurrence'] !== (int)$recurrence)
            {                
                //if recurrence change - change it immediately in $this->actions array
                return false;
            }
        }

        return isset($this->actions[$id]);
    }

    public function remove($id)
    {
        unset($this->actions[$id]);
        $this->update();
    }

    public function update()
    {
        update_option($this->cron_key, $this->actions);
    }

}


