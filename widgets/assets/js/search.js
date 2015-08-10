// JavaScript Document

function Search () {
	NitmEntity.call(this, arguments);
	
	var self = this;
	this.id = 'search';
	this.selfInit = true;
	this.modal;
	this.isActive = false;
	this.modalOptions = {
		'show': false
	};
	this.events = [
		'keypress'
	];
	this.modalId = '#search-modal';
	this.searchField = '#search-field';
	this.resultContainer = '#search-results';
	this.resultWrapper = '#search-results-container';
	this.defaultInit = [
		'initMetaActions'
	];
	
	this.initSearch = function (id, type) {
		var self = this;
		var $container = $(id);
		var $form = $container.find('form');
		$form.on('submit', function (event) {
			event.preventDefault();
			self.operation(this, function (result, form) {
				var $resultWrapper = $container.find(self.resultWrapper);
				$resultWrapper.html(result.data);
				$form.find(self.searchField).val(result.query);
				$resultWrapper.slideDown();
			});
		});
		
		switch(type)
		{
			case 'modal':
			this.initSearchModal($container, $form);
			break;
			
			case 'bar':
			this.initSearchBar($container, $form);
			break;
		}
	}
	
	this.initSearchBar = function($container, $form) {
		var self = this;
		$container.find(this.resultWrapper).map(function (index, wrapper) {
			var $wrapper = $(wrapper);
			$(document).not($wrapper).on('focus, click', function (event) {
				if(!$(self.searchField).is(':focus') && $wrapper.has(event.target).length === 0)
					$wrapper.slideUp();
			});
			$form.find(self.searchField).on('focus', function (event) {
				$wrapper.slideDown();
			});
		});
	}
	
	this.initSearchModal = function ($container, $form) {
		$.map(self.events, function (event) {
			$(document).on(event, function (e) {
				if(self.isActive)
					return;
				//If any special keys were hit then ignore this
				var char = String.fromCharCode(e.which);
				switch(true)
				{
					case $(e.target).is('input, textarea, .redactor-editor'):
					case e.ctrlKey || e.shiftkey || e.altKey || e.metaKey:
					case Array(
						'Esc', 'Escape', 'Backspace', 'Delete',
						'F1', 'F2', 'F3', 'F4', 
						'F5', 'F6', 'F7', 'F8', 
						'F7', 'F10', 'F11', 'F12'
					).indexOf(e.key) != -1:
					case !/\w/.test(char):
					return;
					break;
				}
				
				if($container.modal() == undefined)
				{
					$form.find(self.searchField).focus().val(e.key);
					$container.on('hidden.bs.modal', function (e) {
						self.isActive = false;
						self.modal.modal('hide');
						e.stopPropagation();
					});
					$container.on('shown.bs.modal', function () {
						self.isActive = true;
						var $modal = $(this);
						var $form = $(this).find('form');
						var $input = $form.find(self.searchField);
						$input.focus().val(e.key).get(0).setSelectionRange($input.val().length*2, $input.val().length*2);
					});
					$container.modal(self.modalOptions);
				}
				if(!self.isActive)
				{
					$container.modal('show');
				}
			});
		});
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Search());
});