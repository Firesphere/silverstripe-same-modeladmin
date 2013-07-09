<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of SameModelAdmin
 *
 * @author Simon 'Sphere' Erkelens
 */
class SameModelAdmin extends ModelAdmin {
	
	private static $splitted_models = array();
	
	/**
	 * Returns managed models' create, search, and import forms
	 * @uses SearchContext
	 * @uses SearchFilter
	 * @return SS_List of forms 
	 */
	protected function getManagedModelTabs() {
		$models = $this->getManagedModels();
		$forms  = new ArrayList();
		
		foreach($models as $class => $options) { 
			debug::dump($options);
			debug::dump($class);
			$origClass = explode('_',$options['class']);
			$forms->push(new ArrayData(array (
				'Title'     => $options['title'],
				'ClassName' => $origClass[0],
				'Link' => $this->Link($this->sanitiseClassName($origClass[0])),
				'LinkOrCurrent' => ($origClass == $this->modelClass) ? 'current' : 'link'
			)));
		}
		
		return $forms;
	}
	
	/**
	 * @return array Map of class name to an array of 'title' (see {@link $managed_models})
	 */
	public function getManagedModels() {
		$models = $this->stat('splitted_models');
		if(is_string($models)) {
			user_error(
				'SameModelAdmin::getSplittedModels(): 
				I can\'t split a string. Sorry!
				If you declared statics, make sure they are set to "private"', 
				E_USER_ERROR
			);
		}
		if(!count($models)) {
			user_error(
				'SameModelAdmin::getSplittedModels(): 
				You need to specify at least one DataObject subclass in public static $managed_models.
				Make sure that this property is defined, and that its visibility is set to "public"', 
				E_USER_ERROR
			);
		}

		// Normalize models to have their model class in array key
		foreach($models as $k => $v) {
			if(is_numeric($k)) {
				user_error(
					'SameModelAdmin::getSplittedModels(): 
					I don\'t understand what model you are trying to use', 
					E_USER_ERROR
				);
			}
			else{
				foreach($v as $key => $value){
					if($key == 'split'){
						$tosplit = $k.'_'.$key;
						$models[$tosplit] = array(
							'class' => $k,
							'split' => $key
						);
					}
					$models[$tosplit]['title'] = singleton($k)->i18n_singular_name();
				}
			}
			unset($models[$k]);
		}
		return $models;
	}
}
