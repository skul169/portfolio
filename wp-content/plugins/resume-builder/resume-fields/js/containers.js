window.resume = window.resume || {};

(function($) {

	var resume = window.resume;

	/*
	|--------------------------------------------------------------------------
	| Base Container MODEL
	|--------------------------------------------------------------------------
	|
	| This class represents the default model for a container.
	| The app will fallback to this class if a container has no dedicated model.
	|
	| A model is responsible for holding the containers current state (data).
	| It also holds all the logic surrounding the data management, like: 
	|  - conversion
	|  - validation
	|  - access control
	|
	*/
	resume.containers.Model = Backbone.Model.extend({
		defaults: {
			'error': false,
			'visible': true,
			'has_changes': false,
			'classes': [],
		},

		initialize: function() {
			this.addClass(['resume-container', 'resume-container-' + this.get('type')]);
		},

		addClass: function(newClass) {
			if (!_.isArray(newClass)) {
				newClass = [newClass];
			}

			var oldClasses = this.get('classes') || [];
			var classes = _.union(oldClasses, newClass);

			if (classes.length !== oldClasses.length) {
				this.set('classes', classes);
			}
		},

		/*
		 * The validate method is an internal Backbone method.
		 * @see http://backbonejs.org/#Model-validate
		 */
		validate: function(attrs, options) {
			if (attrs.visible === false) {
				return false;
			}

			var hasErrors = false;
			var view = resume.views[this.get('id')];

			if (!view) {
				return;
			}

			_.each(view.fieldsCollection.models, function(field) {
				if (!field.isRequired()) {
					return;
				}

				if (field.isValid()) {
					field.set('error', false);
				} else {
					field.set('error', true);
					hasErrors = true;
					return;
				}
			});

			this.set('error', hasErrors);

			if (hasErrors) {
				return resume_containers_l10n.please_fill_the_required_fields;
			}
		}
	});

	/*
	|--------------------------------------------------------------------------
	| Base Container VIEW
	|--------------------------------------------------------------------------
	|
	| Responsible for fields initialization and rendering.
	| Updates the container DOM (visibility, errors, etc..).
	| The app will fallback to this class if a container has no dedicated view.
	|
	| Views reflect what the applications data models look like.
	| They also listen to events and react accordingly.
	|
	| @element: .container-[id]
	| @holder:  resume.views[id]
	|
	*/
	resume.containers.View = Backbone.View.extend({
		/*
		 * Set the view DOM events
		 */
		events: {
			"click ul.resume-tabs-nav a": "switchTab",
			"switchToTab .resume-tab": "switchTab"
		},

		/*
		 * Used to include additional variables that can be used inside the template
		 * Can be extended on the "field:beforeRender" event.
		 */
		templateVariables: {},

		initialize: function() {
			this.fieldsCollection = new resume.fields.Collection(this.model.get('fields'));

			// Listen for adding fields in the collection and render them
			this.listenTo(this.fieldsCollection, 'add', this.renderField);

			// Listen for model changes in the fields collection.
			this.listenToOnce(this.fieldsCollection, 'change:value', this.changeListener);

			// Listen for container class updates
			this.listenTo(this.model, 'change:classes', this.updateClass);

			// Check the container visibility before it's rendered.
			// Containers are visible by default. Overwrite the checkVisibility method for custom logic.
			this.on('container:beforeRender', this.checkVisibility);

			// Initialize fields rendering after the container has rendered
			this.on('container:rendered', this.afterRenderInit);

			// Handle tab setup
			this.on('container:rendered', this.setupTabs);

			// Propagate an event to all fields in this container
			this.on('propagate', function(event) {
				this.eventPropagator( this.fieldsCollection, event );
			});

			// Wrap fields that should be on one row
			this.on('layoutUpdated', this.addFieldRows);

			// Check for not saved changes on "onbeforeunload"
			this.on('container:rendered form:invalid', this.onSaveAlert);
			this.on('container:rendered form:invalid', this.showFirstHightlithedFieldTab);
			this.on('form:valid', this.resetOnBeforeUnload);

			// Listen to visibility change
			this.listenTo(this.model, 'change:visible', this.toggleVisibility);

			// Initial visibility check
			this.toggleVisibility(this.model);
		},

		changeListener: function(model, collection) {
			this.model.set('has_changes', true);
		},

		checkVisibility: function() {
			this.model.set('visible', true);
		},

		toggleVisibility: function(model) {
			var id = model.get('id');
			var visible = model.get('visible');
			var $holder = resume.views.main.$body.find('#' + id);

			$holder.toggle(visible);
		},

		eventPropagator: function(collection, event) {
			var collection = collection.toJSON();

			for (var i = 0; i < collection.length; i++) {
				var fieldId = collection[i].id;
				var view = resume.views[fieldId];

				if ( !_.isUndefined(view) ) {
					view.trigger(event.type, event);
					view.trigger('propagate', event);
				}
			}
		},

		addFieldRows: function() {
			var $fields = this.$(':not(.resume-fields-row) > .resume-field.has-width');
			var $group = $();
			var groupWidth = 0;

			var wrapGroup = function() {
				if ($group.length > 0) {
					$group.wrapAll('<div class="resume-fields-row"/>');
					$group = $();
					groupWidth = 0;
				}
			}

			$fields.each(function() {
				var matches = this.className.match(/width-(\d+)/);
				var width = parseInt(matches[1]);

				// stupid repetition; fix when brain is working
				if (groupWidth + width > 100) {
					wrapGroup();
				}

				groupWidth += width;
				$group = $group.add($(this));

				if (!$(this).next().hasClass('has-width')) {
					wrapGroup();
				}
			});
		},

		updateClass: function(model) {
			var classes = model.get('classes');

			this.$el.closest('.resume-container').addClass(classes.join(' '));
		},

		showFirstHightlithedFieldTab: function() {
			this.$el.find('.resume-highlight').first().closest('.resume-tab').trigger('switchToTab');
		},

		onSaveAlert: function() {
			var _this = this;
			var oldCallback = window.onbeforeunload || $.noop;

			window.onbeforeunload = function() {
				var hasChanges = _this.model.get('has_changes');
				var postL10n = postL10n || {};
				var alert = postL10n.saveAlert ? postL10n.saveAlert : resume_containers_l10n.changes_made_save_alert

				if (hasChanges && alert) {
					return alert;
				}

				return oldCallback();
			}
		},

		resetOnBeforeUnload: function() {
			window.onbeforeunload = null;
		},

		render: function() {
			var type = this.model.get('type');
			var tabsTemplate = resume.template('tabs');
			var template = resume.template(type);
			var settings = this.model.attributes.settings;
			var fields = this.fieldsCollection.toJSON();
			var tabs = {};

			var templateVariables, containerHTML;

			this.trigger('container:beforeRender');

			if (settings.tabs) {
				_.each(settings.tabs, function (fieldNames, tab) {
					var tabFields = [];
					var field;

					for (var i = 0; i < fieldNames.length; i++) {
						for (var j = 0; j < fields.length; j++) {
							field = fields[j];

							if (fieldNames[i] === field.name) {
								tabFields.push(field)
							}
						}
					}

					tabs[tab] = {
						fields: tabFields
					};
				});

				_.map(tabs, function (tab) {
					templateVariables = _.extend(this.templateVariables, this.model.attributes, {
						container: this.model,
						fields: tab.fields
					});

					tab.html = template(templateVariables);

					return tab;
				}, this);

				containerHTML = tabsTemplate({
					tabs: tabs
				});
			} else {
				this.templateVariables = _.extend(this.templateVariables, this.model.attributes, {
					container: this.model,
					fields: fields
				});
				containerHTML = template(this.templateVariables);
			}

			this.$el.html(containerHTML);
			this.trigger('container:rendered');

			return this;
		},

		afterRenderInit: function() {
			var _this = this;

			// Trigger the add event on the collection, this should initialize the fields rendering
			this.fieldsCollection.each(function(model) {
				_this.fieldsCollection.trigger('add', model);
			});
		},

		setupTabs: function() {
			var $tabsContainer = this.$el.find('.resume-tabs');

			if ($tabsContainer.length === 0) {
				// this is not a tabbed container, ignore
				return;
			}
			
			var topPositions = this.$('.resume-tabs-nav li').map(function() {
				return $(this).offset().top;
			});

			if(_.uniq(topPositions).length > 1) {
				$tabsContainer.addClass('resume-tabs-stacked');
			}

			// Open the first tab, if none is open yet. 
			if ( ! $tabsContainer.find('ul.resume-tabs-nav li.active').length ) {
				$tabsContainer.find('ul.resume-tabs-nav a:first').trigger('click');
			}
			
		},

		switchTab: function (e) {
			var $element = $(e.target);
			var $li, activeTabIndex;

			if ($element.is('a')) {
				$li = $(e.target).closest('li');
				activeTabIndex = $li.index();
			} else if ($element.is('.resume-tab')) {
				activeTabIndex = $element.index();
				$li = $element.closest('.resume-tabs').find('.resume-tabs-nav li:eq(' + activeTabIndex + ')');
			}

			var $tabs = $('.resume-tab', this.$el);

			$tabs
				.removeClass('active')
				.eq(activeTabIndex).addClass('active');

			$li.addClass('active').siblings().removeClass('active');

			e.preventDefault();
		},

		// Render a field when its added in the fieldsCollection
		renderField: function(model) {
			var _this = this;
			var type = model.get('type');
			var id = model.get('id');

			var FieldView = resume.fields.View[type];
			if ( _.isUndefined(FieldView) ) {
				FieldView = resume.fields.View; // Fallback to the base view
			}

			resume.views[id] = new FieldView({
				el: '.' + id,
				model: model
			});

			resume.views[id].on('field:rendered layoutUpdated', function() {
				_this.trigger('layoutUpdated');
			});

			resume.views[id].render();
		},

		validateForm: function(event) {
			var _this = event.data; 			// the view object
			var $target = $(event.currentTarget);
			var $errorHolder = $('.resume-error-required');
			var $spinner = $target.find('#publishing-action .spinner');
			var valid = _this.model.isValid(); 	// this method will also set the validationError
			var errorText = _this.model.validationError;

			$spinner.addClass('is-active');

			if (valid) { 						// valid
				_this.trigger('form:valid');

				if (event.type === 'click') {
					$errorHolder.slideUp(function() {
						$(this).remove();
					});
				}
			} else { 							// invalid
				_this.trigger('form:invalid');

				$spinner.addClass('disabled');

				if (errorText) {
					if ($errorHolder.length) {
						$errorHolder.find('strong').text(errorText);
					} else {
						$errorHolder = $('<div class="settings-error error hidden below-h2 resume-error-required"><p><strong>' + errorText + '</strong></p></div>');
						$errorHolder.insertAfter('#wpbody-content > .wrap > h2').slideDown();
					}
					var $firstErrorField = $('.resume-highlight :input:first');
					
					// Expand the post meta box if it's closed
					$firstErrorField.closest('.postbox').removeClass('closed');

					// Focus the first error field. 
					$firstErrorField.focus();
				}

				setTimeout(function() {
					if ($target.is('#post')) {
						$target.find('#publish').removeClass('button-primary-disabled button-disabled disabled');
						$target.find('#ajax-loading, #publishing-action .spinner').attr('style','');
					}

					$spinner.removeClass('disabled');
					$spinner.removeClass('is-active');
				}, 0);

				if (event.type === 'click') {
					event.stopImmediatePropagation();
				}

				event.preventDefault();
			}
		}
	});

	/*
	|--------------------------------------------------------------------------
	| Base Container COLLECTION
	|--------------------------------------------------------------------------
	|
	| Holds a set of container models.
	| Also includes model class initialization logic.
	| 
	*/
	resume.containers.Collection = Backbone.Collection.extend({
		model: function(attrs, options) {
			var ContainerModel = resume.containers.Model[attrs.type];

			// Set the container model. If the model is not found, fallback to the base model
			if ( _.isUndefined(ContainerModel) ) {
				ContainerModel = resume.containers.Model; // Fallback to the base model
			}

			return new ContainerModel(attrs, options);
		}
	});

	/******************************** BASE END ********************************/



	/*--------------------------------------------------------------------------
	 * CUSTOM FIELDS
	 *------------------------------------------------------------------------*/

	// CustomFields MODEL
	resume.containers.Model.CustomFields = resume.containers.Model.extend({
		defaults: {
			'page_template': 'default',
			'level': 1,
			'parent_id': null,
			'post_format': null,
			'terms': []
		}
	});

	// CustomFields VIEW
	resume.containers.View.CustomFields = resume.containers.View.extend({
		initialize: function() {
			resume.containers.View.prototype.initialize.apply(this);

			this.$form = this.$el.closest('form#post');
			this.$form.on('submit', null, this, this.validateForm);

			this.syncTemplate();
			this.syncParent();
			this.syncLevel();
			this.syncPostFormat();
			this.syncTaxonomy();

			this.listenTo(this.model, 'change:page_template change:parent_id change:level change:post_format change:terms', this.checkVisibility);
		},

		syncTemplate: function() {
			var _this = this;
			var $select = $('select#page_template');

			$select
				.on('change', function(event) {
					var template = $(this).val();

					_this.model.set('page_template', template);
				})
				.trigger('change');
		},

		syncParent: function() {
			var _this = this;
			var $select = $('select#parent_id');

			$select
				.on('change', function(event) {
					var parentId = parseInt($(this).val());

					_this.model.set('parent_id', parentId);
				})
				.trigger('change');
		},

		syncLevel: function() {
			var _this = this;
			var $select = $('select#parent_id');

			$select
				.on('change', function(event) {
					var levelClass = $(this).find('option:checked').attr('class');
					var level = levelClass ? parseInt(levelClass.match(/^level-(\d+)/)[1]) + 2: 1;

					_this.model.set('level', level);
				})
				.trigger('change');
		},

		syncPostFormat: function() {
			var _this = this;
			var $radio = $('input[name="post_format"]');

			$radio
				.on('change', function(event) {
					var checked = $(this).is(':checked');
					var postFormat = $(this).val();

					if (checked) {
						_this.model.set('post_format', postFormat);
					}
				})
				.trigger('change');
		},

		syncTaxonomy: function() {
			var _this = this;
			var settings = this.model.get('settings');
			var taxonomy = settings.show_on.tax_slug;
			var termId = settings.show_on.tax_term_id;
			var $holder = $('#taxonomy-' + taxonomy);
			var $input = $('#' + taxonomy + 'checklist input');

			if (!taxonomy || !termId || !$holder.length) {
				return false;
			}

			$holder.on('change', $input.selector, function(event) {
				var checked = $(this).is(':checked');
				var termId = parseInt($(this).val());
				var terms = _this.model.get('terms');

				if (checked) {
					terms.push(termId);
				} else {
					var index = terms.indexOf(termId);

					if (index !== -1) {
						terms.splice(index, 1);
					}
				}

				terms = _.uniq(terms);

				_this.model.set('terms', terms);
				_this.model.trigger('change:terms');
			});

			$input.trigger('change')
		},

		checkVisibility: function(model) {
			var _this = this;
			var settings = this.model.get('settings');
			var visible = true;

			_.each(settings.show_on, function(req, type) {
				if ( !req || ( _.isArray(req) && _.isEmpty(req) ) ) {
					return;
				}

				switch(type) {
					case 'template_names':
						var template = _this.model.get('page_template');

						if ($.inArray(template, req) === -1) {
							visible = false;
						}
					break;

					case 'not_in_template_names':
						var template = _this.model.get('page_template');
						
						if ($.inArray(template, req) !== -1) {
							visible = false;
						}
					break;

					case 'parent_page_id':
						var parentId = _this.model.get('parent_id');

						if (parentId != req) {
							visible = false;
						}
					break;

					case 'level_limit':
						var level = _this.model.get('level');


						if (level != req) {
							visible = false;
						}
					break;

					case 'post_formats':
						var post_format = _this.model.get('post_format');

						if ($.inArray(post_format, req) === -1) {
							visible = false;
						}
					break;

					case 'tax_slug':
						var terms = _this.model.get('terms');
						var termId = settings.show_on.tax_term_id;

						if ($.inArray(termId, terms) === -1) {
							visible = false;
						}
					break;
				}
			});

			this.model.set('visible', visible);
		}
	});


	/*--------------------------------------------------------------------------
	 * THEME OPTIONS
	 *------------------------------------------------------------------------*/

	// ThemeOptions VIEW
	resume.containers.View.ThemeOptions = resume.containers.View.extend({
		initialize: function() {
			resume.containers.View.prototype.initialize.apply(this);

			var _this = this;

			this.$form = this.$el.closest('form#theme-options-form');
			this.$form.on('submit', null, this, this.validateForm);

			$(window).on('scroll', function() {
				_this.positionActionPanel.apply(_this);
			});
		},

		positionActionPanel: function() {
			var $panel = $('#postbox-container-1');
			var topOffset = $('#wpadminbar').height() + 10;
			var top = this.$el.offset().top - topOffset;
			var scrollTop = $(window).scrollTop();

			if (scrollTop >= top) {
				$panel.addClass('fixed').css('top', topOffset);
			} else {
				$panel.removeClass('fixed');
			}
		},

		setupTabs: function() {
			var $tabsContainer = this.$el.find('.resume-tabs');
			if ($tabsContainer.length === 0) {
				// this is not a tabbed container, ignore
				return;
			}

			var $tabLinks = $tabsContainer.find('ul.resume-tabs-nav a');
			var currentTabString = window.location.hash.replace(/^#/, '');

			// Retrieve the current tab
			var $currentTabLink = $tabLinks.filter(function() {
				return '!' + $(this).text() === currentTabString;
			}).eq(0);

			// If there is no current tab, use the first one
			if ( ! $currentTabLink.length ) {
				var $currentTabLink = $tabLinks.eq(0);
			}

			// Open the current tab
			$currentTabLink.trigger('click');

			resume.containers.View.prototype.setupTabs.apply(this);
		},

		switchTab: function (e) {
			resume.containers.View.prototype.switchTab.apply(this, [e]);

			var $element = $(e.target);
			var $li;

			if ($element.is('a')) {
				$li = $element.closest('li');
			} else if ($element.is('.resume-tab')) {
				var activeTabIndex = $element.index();
				$li = $element.closest('.resume-tabs').find('.resume-tabs-nav li:eq(' + activeTabIndex + ')');
			}

			window.location.hash = '!' + $li.text();
		}
	});


	/*--------------------------------------------------------------------------
	 * TERM META
	 *------------------------------------------------------------------------*/

	// TermMeta VIEW
	resume.containers.View.TermMeta = resume.containers.View.extend({
		initialize: function() {
			resume.containers.View.prototype.initialize.apply(this);

			var _this = this;

			/* Edit Form */
			this.$editForm = this.$el.closest('form#edittag');
			this.$editForm.on('submit', null, this, this.validateForm);

			/* Add Form */
			this.$addForm = this.$el.closest('form#addtag');
			this.$submitButton = this.$addForm.find('#submit');

			// Fields validation should run before WP validation.
			// The 'bindFirst'custom method is used to move our event at the top of the events stack.
			// This is required because WP stops the click event propagation.
			this.$submitButton.bindFirst('click', this, this.validateForm);

			/* Ajax Monitor */
			resume.views.main.$el.ajaxSuccess(function() {
				_this.initMonitor.apply(_this, arguments);
			});
		},

		initMonitor: function(event, jqXHR, ajaxOptions) {
			if (jqXHR.status != 200 || !ajaxOptions.data.length) {
				return;
			}

			var id = this.model.get('id');
			if (ajaxOptions.data.indexOf('resume_panel_' + id) !== -1) {
				resume.collections.containers.reset();
			}
		}
	});


	/*--------------------------------------------------------------------------
	 * USER META
	 *------------------------------------------------------------------------*/

	// UserMeta MODEL
	resume.containers.Model.UserMeta = resume.containers.Model.extend({
		defaults: {
			'role': null
		}
	});

	// UserMeta VIEW
	resume.containers.View.UserMeta = resume.containers.View.extend({
		initialize: function() {
			resume.containers.View.prototype.initialize.apply(this);

			this.$form = this.$el.closest('form#your-profile, form#createuser');
			this.$form.on('submit', null, this, this.validateForm);

			this.syncRole();

			this.listenTo(this.model, 'change:role', this.checkVisibility);
		},

		syncRole: function() {
			var _this = this;
			var $select = $('select#role');
			var profileRole = this.$el.data('profile-role');

			this.model.set('role', profileRole);

			$select.on('change', function(event) {
				var role = $(this).val();

				_this.model.set('role', role);
			});

		},

		checkVisibility: function(model) {
			var _this = this;
			var settings = this.model.get('settings');
			var profileRole = this.model.get('role');
			var roles = settings.show_on.role || [];
			var visible = true;

			if (roles.length && $.inArray(profileRole, roles) === -1) {
				visible = false;
			}

			this.model.set('visible', visible);
		}
	});


	/*--------------------------------------------------------------------------
	 * WIDGETS
	 *------------------------------------------------------------------------*/

	// Widget MODEL
	resume.containers.Model.Widget = resume.containers.Model.extend({
		initialize: function() {
			resume.containers.Model.prototype.initialize.apply(this);

			var cid = this.cid;
			var fields = this.get('fields');

			_.each(fields, function(field) {
				field.id = field.id + '-' + cid;
				field.lazyload = false; // disable lazyloading in widgets
			});

			this.set('fields', fields);
		}
	});

	// Widget VIEW
	resume.containers.View.Widget = resume.containers.View.extend({
		initialize: function() {
			resume.containers.View.prototype.initialize.apply(this);

			this.$submitButton = this.$el.closest('form').find('input[type="submit"]');
			this.$submitButton
				.off('click', this.validateForm)
				.on('click', null, this, this.validateForm);
		}
	});

}(jQuery));