<?php

class subscription extends itemdef {
	
	public $subscription_type;
	public $type;
	public $label;
	public $single_label;
	public $vars = array();

	
	/*
		
	Generic item type = Subscription
	Subscription type = 'jobalert'
	Subscription item type = 'job'	
	
		
	Steps
	=====
	
	Get subcriptions of suscription_type = jobalert AND no older than days expired in subscription definition
	For each subscription, build appropriate query args
	Run the query
	Foreach results append to email template
	Send email (SES)
		
	Methods
	=======
	
	Learn from Offertune to segment the process, avoid overloading the server
	> not much to be lifted, just one function
	
	Query subscriptions younger than X (can use core search)
	> Will need to validate email address
	> Will need to validate search params
	
	Query jobs to be included in email (can use core search)
	
	Build email
	> Will need to iterate through results and built appropriate markup
	
	Send email
		
		
	*/
	


	function __construct($subscription_type){
				
		$this->subtype = $subscription_type;
		
		// get subscriptions
		$subscriptions = $this->getSubscriptions($this->subtype);
		//echo '<pre>'; print_r($subscriptions); echo '</pre>';
		
		// iterate through subscriptions
		foreach($subscriptions->posts as $subscription){
			
			//echo '<pre>'; print_r($subscription); echo '</pre>';

			// get items that match subscription
			$subscriber_items = $this->getSubscriberItems($subscription->meta['item_type'],$subscription->meta['industry']);
			//echo '<pre>'; print_r($subscriber_items); echo '</pre>';
			
/*
			foreach($subscriber_items->posts as $item){
				echo '<pre>';
				print_r($item->meta['job_title']);
				echo '</pre>';
			}
			
			echo 'next sub';
*/

			$this->sendSubscription($subscription,$subscriber_items->posts);
		}
		
		//echo '<pre>Subscriptions: <br />'; print_r($this->getSubscriptions('jobalert')); echo '</pre>';

	}
	
	public function getSubscriptions($subtype){
		
		// this needs to handle when a subscription expires eg. 90 days
		
		$params = array(
				'type' => 'subscription',
				'expire' => array('subscription_date' => 90),
				'meta_query' => array(
					array(
					 	'key' => 'subscription_type',
					 	'value' => $subtype
					),
					array(
					 	'key' => 'status',
					 	'value' => 'active'
					)

				)				
		);
				
		return directory_search($params);
		
	}
	
	public function getSubscriberItems($item_type,$industry){
		
		// this needs to handle when a item_type (job) expires eg. 7 days

		$params = array(
				'type' => $item_type,
				'expire' => array('publish_from' => 1007),
				'industry' => $industry,
				'job_status' => 'published'
		);

		return directory_search($params);
		
	}
	
	public function sendSubscription($subscription,$subscriber_items){
		
		$data['notify'] = $subscription->meta['subscriber_email'];
		$data['notify_template'] = $subscription->meta['subscription_type'];
		
		foreach($subscriber_items as $item){
			
			$item_display .= '<tr class="clickable rowitem"><td>';
			
			//echo '<pre>'; print_r($item); echo '</pre>';
			$item_display .= '<h3><a href="'.get_site_url().'/job?i='.$item->ID.'">'.$item->meta['job_title'].'</a></h3>';
			$item_display .= '<h4>'.$item->meta['salary_details'];
			if($item->meta['location'] != '') $item_display .= ', '.$item->meta['location'];
			$item_display .= '</h4>';


			$description = $item->meta['ad_type'] == 'standard' ? $item->meta['full_description_limited'] : $item->meta['full_description'];
			

			$description = strip_tags($description);
			$description = explode(' ',$description);
			$description = array_filter($description);
			$description = array_slice($description, 0, 30);
			$description = implode(' ', $description);
			$description .= '... <a href="'.get_site_url().'/job?i='.$item->ID.'">Read more</a>';
			
			$item_display .= '<p>'.$description.'</p>';
			
			$item_display .= '</td></tr>';
		}
		
		$data['repeat'] = $item_display;
		
		$this->notify($data);

			
			// business logic, DOESN'T BELONG HERE
			// This date logic being overridden by 'expires' behaviour in directory_search
			
/*
			if($params[$subscription->meta['expire']]){
				foreach($params['expire'] as $k=>$v)
				$expires = strtotime('now') - ($v*24*60*60);
				echo '<pre>Expires: '.$expires.'</pre>';
				$params['meta_query'][] = array('key' => $k, 'value' => $expires, 'compare' => '>');	
			}
*/


			
		
		
	}


	
	
			
}