var ET_PageBuilder = ET_PageBuilder || {};

window.wp = window.wp || {};

/**
 * The builder version and product name will be updated by grunt release task. Do not edit!
 */
window.et_builder_version = '3.0.60';
window.et_builder_product_name = 'Divi';

( function($) {
	var et_error_modal_shown = window.et_error_modal_shown,
		et_is_loading_missing_modules = false,
		et_pb_bulder_loading_attempts = 0,
		et_pb_hovered_item_buffer = {},
		et_pb_all_unsynced_options = {},
		et_pb_all_legacy_synced_options = [],
		et_pb_key_pressed = {
			's' : false,
			'r' : false,
			'c' : false
		};

	function et_builder_load_backbone_templates( reload_template ) {

		// run et_pb_append_templates as many times as needed
		var et_pb_templates_count = 0,
			date_now              = new Date(),
			today_date            = date_now.getYear() + '_' + date_now.getMonth() + '_' + date_now.getDate(),
			et_ls_prefix          = 'et_pb_templates_',
			et_ls_all_modules     = ( et_pb_options['et_builder_module_parent_shortcodes'] + '|' + et_pb_options['et_builder_module_child_shortcodes'] ).split( '|' ),
			product_version       = et_pb_options.product_version,
			local_storage_buffer  = '',
			processed_modules_count = 0,
			reload_template = _.isUndefined( reload_template ) ? false : reload_template,
			missing_modules = {
				missing_modules_array: []
			},
			et_pb_templates_interval;

		if ( ! reload_template ) {
			if ( ! $( 'script[src="' + et_pb_options.builder_js_src + '"]' ).length ) {
				$( '.et-pb-cache-update' ).show();
			}

			$( 'body' ).on( 'click', '.et_builder_increase_memory', function() {
				var $this_button = $(this);

				$.ajax({
					type: "POST",
					dataType: 'json',
					url: et_pb_options.ajaxurl,
					data: {
						action : 'et_pb_increase_memory_limit',
						et_admin_load_nonce : et_pb_options.et_admin_load_nonce
					},
					success: function( data ) {
						if ( ! _.isUndefined( data.success ) ) {
							$this_button.addClass( 'et_builder_modal_action_button_success' ).text( et_pb_options.memory_limit_increased );
						} else {
							$this_button.addClass( 'et_builder_modal_action_button_fail' ).prop( 'disabled', true ).text( et_pb_options.memory_limit_not_increased );
						}
					}
				});

				return false;
			} );

			$( 'body' ).on( 'click', '.et_pb_reload_builder', function() {
				location.reload();

				return false;
			} );

		}

		if ( et_should_load_from_local_storage() ) {
			for ( et_ls_module_index in et_ls_all_modules ) {
				var et_ls_module_slug      = et_ls_all_modules[ et_ls_module_index ],
					et_ls_template_slug    = et_ls_prefix + et_ls_module_slug,
					et_ls_template_content = localStorage.getItem( et_ls_template_slug );

				// count the processed modules
				processed_modules_count++;

				if ( _.isUndefined( et_ls_template_content ) || _.isNull( et_ls_template_content ) ) {
					missing_modules['missing_modules_array'].push( et_ls_module_slug );
				} else {
					local_storage_buffer += localStorage.getItem( et_ls_template_slug );
				}

				// perform ajax request if missing_modules_array length equals to the templates amount setting or if all the modules processed and we need to retrieve something
				if ( ! et_is_loading_missing_modules && ( ( missing_modules['missing_modules_array'].length === parseInt( et_pb_options.et_builder_templates_amount ) ) || ( missing_modules['missing_modules_array'].length && ( et_ls_all_modules.length === processed_modules_count ) ) ) ) {
					et_is_loading_missing_modules = true;
					$.ajax({
						type: "POST",
						dataType: 'json',
						url: et_pb_options.ajaxurl,
						data: {
							action : 'et_pb_get_backbone_template',
							et_post_type : et_pb_options.post_type,
							et_modules_slugs : JSON.stringify( missing_modules ),
							et_admin_load_nonce : et_pb_options.et_admin_load_nonce
						},
						success: function( data ) {
							et_is_loading_missing_modules = false;

							try {
								localStorage.setItem( et_ls_prefix + data['slug'], data['template'] );
							} catch(e) {
								// do not use localStorage if it full or any other error occurs
							}

							$( 'body' ).append( data.template );
							if ( data.length ) {
								_.each( data, function( single_module ) {
									try {
										localStorage.setItem( et_ls_prefix + single_module['slug'], single_module['template'] );
									} catch(e) {
										// do not use localStorage if it full or any other error occurs
									}

									$( 'body' ).append( single_module['template'] );
								} );
							}
						}
					});

					// reset the array of missing modules
					missing_modules['missing_modules_array'] = [];
				}

			}

			$( 'body' ).append( local_storage_buffer );

		} else {

			// run et_pb_append_templates as many times as needed
			et_pb_templates_interval = setInterval( function() {
				if ( et_pb_templates_count === Math.ceil( et_pb_options.et_builder_modules_count/et_pb_options.et_builder_templates_amount ) ) {
					clearInterval( et_pb_templates_interval );
					return false;
				}

				et_pb_append_templates( et_pb_templates_count * et_pb_options.et_builder_templates_amount );

				et_pb_templates_count++;
			}, 800);

			et_ls_set_transient();

		}

		function et_builder_has_storage_support() {
			try {
				return 'localStorage' in window && window.localStorage !== null;
			} catch (e) {
				return false;
			}
		}

		function et_ls_set_transient() {
			if ( ! et_builder_has_storage_support() ) {
				return false;
			}

			try {
				localStorage.setItem( et_ls_prefix + 'settings_date', today_date );

				localStorage.setItem( et_ls_prefix + 'settings_product_version', product_version );
			} catch(e) {
				// do not use localStorage if it full or any other error occurs
			}
		}

		function et_should_load_from_local_storage() {
			if ( ! et_builder_has_storage_support() ) {
				return false;
			}

			if ( ! _.isUndefined( et_pb_options.force_cache_purge ) && 'true' === et_pb_options.force_cache_purge ) {
				return false;
			}

			var et_ls_settings_date = localStorage.getItem( et_ls_prefix + 'settings_date' ),
				et_ls_settings_product_version = localStorage.getItem( et_ls_prefix + 'settings_product_version' );

			if ( _.isUndefined( et_ls_settings_date ) || _.isNull( et_ls_settings_date ) ) {
				return false;
			}

			if ( _.isUndefined( et_ls_settings_product_version ) || _.isNull( et_ls_settings_product_version ) ) {
				return false;
			}

			if ( today_date != et_ls_settings_date || product_version != et_ls_settings_product_version ) {
				et_remove_ls_templates();

				return false;
			}

			return true;
		}

		function et_remove_ls_templates() {
			if ( ! et_builder_has_storage_support() ) {
				return false;
			}

			var templates_prefix_re = /et_pb_templates_*/i

			for ( var prop in localStorage ) {
				if ( found = prop.match( templates_prefix_re ) ) {
					localStorage.removeItem( prop );
				}
			}
		}

		function et_pb_append_templates( start_from ) {
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: et_pb_options.ajaxurl,
				data: {
					action : 'et_pb_get_backbone_templates',
					et_post_type : et_pb_options.post_type,
					et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
					et_templates_start_from : start_from
				},
				error: function() {
					var $failure_notice_template = $( '#et-builder-failure-notice-template' );

					if ( et_error_modal_shown ) {
						return;
					}

					if ( ! $failure_notice_template.length ) {
						return;
					}

					if ( $( '.et_pb_failure_notification_modal' ).length ) {
						return;
					}

					if ( et_builder_has_storage_support() ) {
						localStorage.removeItem( et_ls_prefix + 'settings_date' );
						localStorage.removeItem( et_ls_prefix + 'settings_product_version' );
					}

					$( 'body' ).addClass( 'et_pb_stop_scroll' ).append( $failure_notice_template.html() );
				},
				success: function( data ) {
					//append retrieved templates to body
					for ( var name in data.templates ) {
						if ( et_builder_has_storage_support() ) {
							try {
								localStorage.setItem( 'et_pb_templates_' + name, data.templates[name] );
							} catch(e) {
								// do not use localStorage if it full or any other error occurs
							}
						}

						$( 'body' ).append( data.templates[name] );
					}
				}
			});
		}

	}
	et_builder_load_backbone_templates();

	$( document ).ready( function() {

		// Explicitly define ERB-style template delimiters to prevent
		// template delimiters being overwritten by 3rd party plugin
		_.templateSettings = {
			evaluate   : /<%([\s\S]+?)%>/g,
			interpolate: /<%=([\s\S]+?)%>/g,
			escape     : /<%-([\s\S]+?)%>/g
		};

		// Models

		ET_PageBuilder.Module = Backbone.Model.extend( {

			defaults: {
				type : 'element',
				_builder_version : et_pb_options.product_version
			}

		} );

		ET_PageBuilder.SavedTemplate = Backbone.Model.extend( {

			defaults: {
				title : 'template',
				ID : 0,
				shortcode : '',
				is_global : 'false',
				layout_type : '',
				module_type : '',
				categories : []
			}

		} );

		ET_PageBuilder.History = Backbone.Model.extend( {

			defaults : {
				timestamp : _.now(),
				shortcode : '',
				current_active_history : false,
				verb : 'did',
				noun : 'something'
			},

			max_history_limit : 100,

			validate : function( attributes, options ) {
				var histories_count = options.collection.length,
					active_history_model = options.collection.findWhere({ current_active_history : true }),
					shortcode            = attributes.shortcode,
					last_model           = _.isUndefined( active_history_model ) ? options.collection.at( ( options.collection.length - 1 ) ) : active_history_model,
					last_shortcode       = _.isUndefined( last_model ) ? false : last_model.get( 'shortcode' ),
					previous_active_histories;

				if ( shortcode === last_shortcode ) {
					return 'duplicate';
				}

				// Turn history tracking off
				ET_PageBuilder_App.enable_history = false;

				// Limit number of history limit
				var histories_count = options.collection.models.length,
					remove_limit = histories_count - ( this.max_history_limit - 1 ),
					ranges,
					deleted_model;

				// Some models are need to be removed
				if ( remove_limit > 0 ) {
					// Loop and shift (remove first model in collection) n-times
					for (var i = 1; i <= remove_limit; i++) {
						options.collection.shift();
					};
				}
			}

		} );

		// helper module
		ET_PageBuilder.Layout = Backbone.Model.extend( {

			defaults: {
				moduleNumber : 0,
				forceRemove : false,
				modules : $.parseJSON( et_pb_options.et_builder_modules ),
				views : [
				]
			},

			initialize : function() {
				// Single and double quotes are replaced with %% in et_builder_modules
				// to avoid js conflicts.
				// Replace them with appropriate signs.
				_.each( this.get( 'modules' ), function( module ) {
					module['title'] = module['title'].replace( /%%/g, '"' );
					module['title'] = module['title'].replace( /\|\|/g, "'" );
				} );
			},

			addView : function( module_cid, view ) {
				var views = this.get( 'views' );

				views[module_cid] = view;
				this.set( { 'views' : views } );
			},

			getView : function( cid ) {
				return this.get( 'views' )[cid];
			},

			getChildViews : function( parent_id ) {
				var views = this.get( 'views' ),
					child_views = {};

				_.each( views, function( view, key ) {
					if ( typeof view !== 'undefined' && view['model']['attributes']['parent'] === parent_id )
						child_views[key] = view;
				} );

				return child_views;
			},

			getChildrenViews : function( parent_id ) {
				var this_el = this,
					views = this_el.get( 'views' ),
					child_views = {},
					grand_children;

				_.each( views, function( view, key ) {
					if ( typeof view !== 'undefined' && view['model']['attributes']['parent'] === parent_id ) {
						grand_children = this_el.getChildrenViews( view['model']['attributes']['cid'] );

						if ( ! _.isEmpty( grand_children ) ) {
							_.extend( child_views, grand_children );
						}

						child_views[key] = view;
					}

				} );

				return child_views;
			},

			getParentViews : function( parent_cid ) {
				var parent_view = this.getView( parent_cid ),
					parent_views = {};

				while( ! _.isUndefined( parent_view ) ) {

					parent_views[parent_view['model']['attributes']['cid']] = parent_view;
					parent_view = this.getView( parent_view['model']['attributes']['parent'] );
				}

				return parent_views;
			},

			getSectionView : function( parent_cid ) {
				var views = this.getParentViews( parent_cid ),
					section_view;

				section_view = _.filter( views, function( item ) {
					if ( item.model.attributes.type === "section" ) {
						return true;
					} else {
						return false;
					}
				} );

				if ( _.isUndefined( section_view[0] ) ) {
					return false;
				} else {
					return section_view[0];
				}
			},

			setNewParentID : function( cid, new_parent_id ) {
				var views = this.get( 'views' );

				views[cid]['model']['attributes']['parent'] = new_parent_id;

				this.set( { 'views' : views } );
			},

			removeView : function( cid ) {
				var views = this.get( 'views' ),
					new_views = {};

				_.each( views, function( value, key ) {
					if ( key != cid )
						new_views[key] = value;
				} );

				this.set( { 'views' : new_views } );
			},

			generateNewId : function() {
				var moduleNumber = this.get( 'moduleNumber' ) + 1;

				this.set( { 'moduleNumber' : moduleNumber } );

				return moduleNumber;
			},

			generateTemplateName : function( name ) {
				var default_elements = [ 'row', 'row_inner', 'section', 'column', 'column_inner'];

				if ( -1 !== $.inArray( name, default_elements ) ) {
					name = 'et_pb_' + name;
				}

				return '#et-builder-' + name + '-module-template';
			},

			getModuleOptionsNames : function( module_type ) {
				var modules = this.get('modules');

				return this.addAdminLabel( _.findWhere( modules, { label : module_type } )['options'] );
			},

			getNumberOf : function( element_name, module_cid ) {
				var views = this.get( 'views' ),
					num = 0;

				_.each( views, function( view ) {
					if ( typeof view !== 'undefined' ) {
						var type = view['model']['attributes']['type'];

						if ( view['model']['attributes']['parent'] === module_cid && ( type === element_name || type === ( element_name + '_inner' ) ) )
							num++;
					}
				} );

				return num;
			},

			getNumberOfModules : function( module_name ) {
				var views = this.get( 'views' ),
					num = 0;

				_.each( views, function( view ) {
					if ( typeof view !== 'undefined' ) {
						if ( view['model']['attributes']['type'] === module_name )
							num++;
					}
				} );

				return num;
			},

			getTitleByShortcodeTag : function ( tag ) {
				var modules = this.get('modules');

				return _.findWhere( modules, { label : tag } )['title'];
			},

			getDefaultAdminLabel : function( module_type ) {
				var is_structure_element = $.inArray( module_type, [ 'section', 'row', 'column', 'row_inner', 'column_inner' ] ) > -1;

				if ( is_structure_element ) {
					return typeof et_pb_options.noun[ module_type ] !== 'undefined' ? et_pb_options.noun[ module_type ] : module_type;
				}

				return this.getTitleByShortcodeTag( module_type );
			},

			isModuleFullwidth : function ( module_type ) {
				var modules = this.get('modules');

				return 'on' === _.findWhere( modules, { label : module_type } )['fullwidth_only'] ? true : false;
			},

			isChildrenLocked : function ( module_cid ) {
				var children_views = this.getChildrenViews( module_cid ),
					children_locked = false;

				_.each( children_views, function( child ) {
					if ( child.model.get( 'et_pb_locked' ) === 'on' || child.model.get( 'et_pb_parent_locked' ) === 'on' ) {
						children_locked = true;
					}
				} );

				return children_locked;
			},

			addAdminLabel : function ( optionsNames ) {
				return _.union( optionsNames, ['admin_label'] );
			},

			removeGlobalAttributes : function ( view, keep_attributes ) {
				var this_class                 = this,
					keep_attributes            = _.isUndefined( keep_attributes ) ? false : keep_attributes,
					global_item_cid            = _.isUndefined( view.model.attributes.global_parent_cid ) ? view.model.attributes.cid : view.model.attributes.global_parent_cid,
					global_item_view           = this.getView( global_item_cid );
					global_item_children_views = this.getChildrenViews( global_item_cid );

				// Modify global item's attributes
				if ( this.is_global( global_item_view.model ) ) {
					if ( keep_attributes ) {
						global_item_view.model.set( 'et_pb_temp_global_module', global_item_view.model.get( 'et_pb_global_module' ) );
					}

					global_item_view.model.unset( 'et_pb_global_module' );
				}

				// Modify global item children's attributes
				_.each( global_item_children_views, function( global_item_children_view ) {
					if ( this_class.is_global_children( global_item_children_view.model ) ) {
						if ( keep_attributes ) {
							global_item_children_view.model.set( 'et_pb_temp_global_parent', global_item_children_view.model.get( 'et_pb_global_parent' ) );
						}

						global_item_children_view.model.unset( 'et_pb_global_parent' );
					}

					if ( this_class.has_global_parent_cid( global_item_children_view.model ) ) {
						if ( keep_attributes ) {
							global_item_children_view.model.set( 'et_pb_temp_global_parent_cid', global_item_children_view.model.get( 'global_parent_cid' ) );
						}

						global_item_children_view.model.unset( 'global_parent_cid' );
					}
				});
			},

			removeTemporaryGlobalAttributes : function ( view, restore_attributes ) {
				var this_class         = this,
					restore_attributes = _.isUndefined( restore_attributes ) ? false : restore_attributes,
					global_item_model = _.isUndefined( view.model.attributes.et_pb_temp_global_module ) ? ET_PageBuilder_Modules.findWhere({ et_pb_temp_global_module : view.model.attributes.et_pb_temp_global_parent }) : view.model,
					global_item_cid   = global_item_model.attributes.cid,
					global_item_view  = ET_PageBuilder_Layout.getView( global_item_cid );
					global_item_children_views = ET_PageBuilder_Layout.getChildrenViews( global_item_cid );

				if ( this.is_temp_global( global_item_view.model ) ) {
					if ( restore_attributes ) {
						global_item_view.model.set( 'et_pb_global_module', global_item_view.model.get( 'et_pb_temp_global_module' ) );
					}

					global_item_view.model.unset( 'et_pb_temp_global_module' );
				}

				_.each( global_item_children_views, function( global_item_children_view ) {
					if ( this_class.is_temp_global_children( global_item_children_view.model ) ) {
						if ( restore_attributes ) {
							global_item_children_view.model.set( 'et_pb_global_parent', global_item_children_view.model.get( 'et_pb_temp_global_parent' ) );
						}

						global_item_children_view.model.unset( 'et_pb_temp_global_parent' );
					}

					if ( this_class.has_temp_global_parent_cid( global_item_children_view.model ) ) {
						if ( restore_attributes ) {
							global_item_children_view.model.set( 'global_parent_cid', global_item_children_view.model.get( 'et_pb_temp_global_parent_cid' ) );
						}

						global_item_children_view.model.unset( 'et_pb_temp_global_parent_cid' );
					}
				});

				if ( restore_attributes ) {
					// Update global template
					et_pb_update_global_template( global_item_cid );
				}
			},

			is_app : function( model ) {
				if ( model.attributes.type === 'app' ) {
					return true;
				}

				return false;
			},

			is_global : function( model ) {
				// App cannot be global module. Its model.get() returns error
				if ( this.is_app( model ) ) {
					return false;
				}

				return model.has( 'et_pb_global_module' ) && model.get( 'et_pb_global_module' ) !== '' ? true : false;
			},

			is_global_children : function( model ) {
				// App cannot be global module. Its model.get() returns error
				if ( this.is_app( model ) ) {
					return false;
				}

				return model.has( 'et_pb_global_parent' ) && model.get( 'et_pb_global_parent' ) !== '' ? true : false;
			},

			has_global_parent_cid : function( model ) {
				return model.has( 'global_parent_cid' ) && model.get( 'global_parent_cid' ) !== '' ? true : false;
			},

			is_temp_global : function( model ) {
				return model.has( 'et_pb_temp_global_module' ) && model.get( 'et_pb_temp_global_module' ) !== '' ? true : false;
			},

			is_temp_global_children : function( model ) {
				return model.has( 'et_pb_temp_global_parent' ) && model.get( 'et_pb_temp_global_parent' ) !== '' ? true : false;
			},

			has_temp_global_parent_cid : function( model ) {
				return model.has( 'et_pb_temp_global_parent_cid' ) && model.get( 'et_pb_temp_global_parent_cid' ) !== '' ? true : false;
			},

			changeColumnStructure: function( that, options, skip_reinit, skip_history ) {
				var layout = options.layout.split(','),
					specialty_columns = options.specialty_columns,
					layout_specialty = options.layout_specialty,
					layout_elements_num = _.size( layout ),
					this_view = that.options.view;
					global_module_cid = et_pb_get_global_parent_cid( that );

				if ( options.is_structure_change ) {
					var row_columns = ET_PageBuilder_Layout.getChildViews( that.model.get( 'cid' ) ),
						columns_structure_old = [],
						index_count = 0;

					_.each( row_columns, function( row_column ) {
						columns_structure_old[index_count] = row_column.model.get( 'cid' );
						index_count = index_count + 1;
					} );
				}

				_.each( layout, function( element, index ) {
					var update_content = layout_elements_num == ( index + 1 )
						? 'true'
						: 'false',
						column_attributes = {
							type : 'column',
							cid : ET_PageBuilder_Layout.generateNewId(),
							parent : that.model.get( 'cid' ),
							layout : element,
							view : this_view
						};

					if ( typeof that.model.get( 'et_pb_global_parent' ) !== 'undefined' && '' !== that.model.get( 'et_pb_global_parent' ) ) {
						column_attributes.et_pb_global_parent = that.model.get( 'et_pb_global_parent' );
						column_attributes.global_parent_cid = that.model.get( 'global_parent_cid' );
					}

					if ( typeof layout_specialty[index] !== 'undefined' && layout_specialty[index] === '1' ) {
						column_attributes.layout_specialty = layout_specialty[index];
						column_attributes.specialty_columns = parseInt( specialty_columns );
					}

					if ( typeof that.model.get( 'specialty_row' ) !== 'undefined' ) {
						that.model.set( 'module_type', 'row_inner', { silent : true } );
						that.model.set( 'type', 'row_inner', { silent : true } );
					}

					that.collection.add( [ column_attributes ], { update_shortcodes : update_content } );
				} );

				if ( options.is_structure_change ) {
					var columns_structure_new = [];

					row_columns = ET_PageBuilder_Layout.getChildViews( that.model.get( 'cid' ) );
					index_count = 0;

					_.each( row_columns, function( row_column ) {
						columns_structure_new[index_count] = row_column.model.get( 'cid' );
						index_count = index_count + 1;
					} );

					// delete old columns IDs
					columns_structure_new.splice( 0, columns_structure_old.length );

					for ( index = 0; index < columns_structure_old.length; index++ ) {
						var is_extra_column = ( columns_structure_old.length > columns_structure_new.length ) && ( index > ( columns_structure_new.length - 1 ) ) ? true : false,
							old_column_cid = columns_structure_old[index],
							new_column_cid = is_extra_column ? columns_structure_new[columns_structure_new.length-1] : columns_structure_new[index],
							column_html = ET_PageBuilder_Layout.getView( old_column_cid ).$el.html(),
							modules = ET_PageBuilder_Layout.getChildViews( old_column_cid ),
							$updated_column,
							column_html_old = '';

						ET_PageBuilder_Layout.getView( old_column_cid ).model.destroy();

						ET_PageBuilder_Layout.getView( old_column_cid ).remove();

						ET_PageBuilder_Layout.removeView( old_column_cid );

						$updated_column = $('.et-pb-column[data-cid="' + new_column_cid + '"]');

						if ( ! is_extra_column ) {
							$updated_column.html( column_html );
						} else {
							$updated_column.find( '.et-pb-insert-module' ).remove();

							column_html_old = $updated_column.html();

							$updated_column.html( column_html_old + column_html );
						}

						_.each( modules, function( module ) {
							module.model.set( 'parent', new_column_cid, { silent : true } );
						} );
					}

					// Enable history saving and set meta for history
					ET_PageBuilder_App.allowHistorySaving( 'edited', 'column' );

					if ( ! skip_reinit ) {
						et_reinitialize_builder_layout();
					}
				}

				if ( typeof that.model.get( 'template_type' ) !== 'undefined' && 'section' === that.model.get( 'template_type' ) && 'on' === that.model.get( 'et_pb_specialty' ) ) {
					et_reinitialize_builder_layout();
				}

				if ( typeof that.model.get( 'et_pb_template_type' ) !== 'undefined' && 'row' === that.model.get( 'et_pb_template_type' ) ) {
					et_add_template_meta( '_et_pb_row_layout', options.layout );
				}

				if ( typeof global_module_cid !== 'undefined' && '' !== global_module_cid ) {
					et_pb_update_global_template( global_module_cid );
				}

				if ( ! skip_history ) {
					// Enable history saving and set meta for history
					ET_PageBuilder_App.allowHistorySaving( 'added', 'column' );
				}

				ET_PageBuilder_Events.trigger( 'et-add:columns' );
			},
		} );

		// Collections

		ET_PageBuilder.Modules = Backbone.Collection.extend( {

			model : ET_PageBuilder.Module

		} );

		ET_PageBuilder.SavedTemplates = Backbone.Collection.extend( {

			model : ET_PageBuilder.SavedTemplate

		} );

		ET_PageBuilder.Histories = Backbone.Collection.extend( {

			model : ET_PageBuilder.History

		} );


		//Views
		ET_PageBuilder.TemplatesView = window.wp.Backbone.View.extend( {
			className : 'et_pb_saved_layouts_list',

			tagName : 'ul',

			render: function() {
				var global_class = '',
					layout_category = typeof this.options.category === 'undefined' ? 'all' : this.options.category;

				this.collection.each( function( single_template ) {
					if ( 'all' === layout_category || ( -1 !== $.inArray( layout_category, single_template.get( 'categories' ) ) ) ) {
						var single_template_view = new ET_PageBuilder.SingleTemplateView( { model: single_template } );
						this.$el.append( single_template_view.el );
						global_class = typeof single_template_view.model.get( 'is_global' ) !== 'undefined' && 'global' === single_template_view.model.get( 'is_global' ) ? 'global' : '';
					}
				}, this );

				if ( 'global' === global_class ) {
					this.$el.addClass( 'et_pb_global' );
				}

				return this;
			}

		} );

		ET_PageBuilder.SingleTemplateView = window.wp.Backbone.View.extend( {
			tagName : 'li',

			template: _.template( $( '#et-builder-saved-entry' ).html() ),

			events: {
				'click' : 'insertSection',
			},

			initialize: function(){
				this.render();
			},

			render: function() {
				this.$el.html( this.template( this.model.toJSON() ) );

				if ( typeof this.model.get( 'module_type' ) !== 'undefined' && '' !== this.model.get( 'module_type' ) && 'module' === this.model.get( 'layout_type' ) ) {
					this.$el.addClass( this.model.get( 'module_type' ) );
				}
			},

			insertSection : function( event ) {
				var clicked_button     = $( event.target ),
					parent_id          = typeof clicked_button.closest( '.et_pb_modal_settings' ).data( 'parent_cid' ) !== 'undefined' ? clicked_button.closest( '.et_pb_modal_settings' ).data( 'parent_cid' ) : '',
					current_row        = typeof $( '.et-pb-settings-heading' ).data( 'current_row' ) !== 'undefined' ? $( '.et-pb-settings-heading' ).data( 'current_row' ) : '',
					global_id          = 'global' === this.model.get( 'is_global' ) ? this.model.get( 'ID' ) : '',
					specialty_row      = typeof $( '.et-pb-saved-modules-switcher' ).data( 'specialty_columns' ) !== 'undefined' ? 'on' : 'off',
					shortcode          = this.model.get( 'shortcode' ),
					update_global      = false,
					global_holder_id   = 'row' === this.model.get( 'layout_type' ) ? current_row : parent_id,
					global_holder_view = ET_PageBuilder_Layout.getView( global_holder_id ),
					history_noun       = this.options.model.get( 'layout_type' ) === 'row_inner' ? 'saved_row' : 'saved_' + this.options.model.get( 'layout_type' ),
					$modal_container   = clicked_button.closest( '.et_pb_modal_settings_container' );

					if ( 'on' === specialty_row ) {
						global_holder_id = global_holder_view.model.get( 'parent' );
						global_holder_view = ET_PageBuilder_Layout.getView( global_holder_id );
					}

					if ( 'section' !== this.model.get( 'layout_type' ) && ( ( typeof global_holder_view.model.get( 'global_parent_cid' ) !== 'undefined' && '' !== global_holder_view.model.get( 'global_parent_cid' ) ) || ( typeof global_holder_view.model.get( 'et_pb_global_module' ) !== 'undefined' && '' !== global_holder_view.model.get( 'et_pb_global_module' ) ) ) ) {
						update_global = true;
					}

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'added', history_noun );

				event.preventDefault();

				// apply wpautop to the shortcode to make sure all the line breaks inserted correctly
				if ( typeof window.switchEditors !== 'undefined' ) {
					shortcode = et_pb_fix_shortcodes( window.switchEditors.wpautop( shortcode ) );
				}

				ET_PageBuilder_App.createLayoutFromContent( shortcode , parent_id, '', { ignore_template_tag : 'ignore_template', current_row_cid : current_row, global_id : global_id, after_section : parent_id, is_reinit : 'reinit' } );
				et_reinitialize_builder_layout();

				if ( true === update_global ) {
						global_module_cid = typeof global_holder_view.model.get( 'global_parent_cid' ) !== 'undefined' ? global_holder_view.model.get( 'global_parent_cid' ) : global_holder_id;

					et_pb_update_global_template( global_module_cid );
				}

				if ( $modal_container.length ) {
					$modal_container.find( '.et-pb-modal-close' ).click();
				}
			}
		} );

		ET_PageBuilder.TemplatesModal = window.wp.Backbone.View.extend( {
			className : 'et_pb_modal_settings',

			template : _.template( $( '#et-builder-load_layout-template' ).html() ),

			events : {
				'click .et-pb-options-tabs-links li a' : 'switchTab'
			},

			render: function() {

				this.$el.html( this.template( { "display_switcher" : "off" } ) );

				this.$el.addClass( 'et_pb_modal_no_tabs' );

				return this;
			},

			switchTab: function( event ) {
				var $this_el = $( event.currentTarget ).parent();
				event.preventDefault();

				et_handle_templates_switching( $this_el, 'section', '' );
			}

		} );

		ET_PageBuilder.SectionView = window.wp.Backbone.View.extend( {

			className : 'et_pb_section',

			template : _.template( $('#et-builder-section-template').html() ),

			events: {
				'click .et-pb-settings-section' : 'showSettings',
				'click .et-pb-clone-section' : 'cloneSection',
				'click .et-pb-remove-section' : 'removeSection',
				'click .et-pb-section-add-main' : 'addSection',
				'click .et-pb-section-add-fullwidth' : 'addFullwidthSection',
				'click .et-pb-section-add-specialty' : 'addSpecialtySection',
				'click .et-pb-section-add-saved' : 'addSavedSection',
				'click .et-pb-expand' : 'expandSection',
				'click .et-pb-insert-row' : 'addFirstRow',
				'contextmenu .et-pb-section-add' : 'showRightClickOptions',
				'click.et_pb_section > .et-pb-controls .et-pb-unlock' : 'unlockSection',
				'contextmenu.et_pb_section > .et-pb-controls' : 'showRightClickOptions',
				'contextmenu.et_pb_row > .et-pb-right-click-trigger-overlay' : 'showRightClickOptions',
				'click.et_pb_section > .et-pb-controls' : 'hideRightClickOptions',
				'click.et_pb_row > .et-pb-right-click-trigger-overlay' : 'hideRightClickOptions',
				'click > .et-pb-locked-overlay' : 'showRightClickOptions',
				'contextmenu > .et-pb-locked-overlay' : 'showRightClickOptions',
				'click' : 'setABTesting',
			},

			initialize : function() {
				this.child_views = [];
				this.listenTo( this.model, 'change:admin_label', this.renameModule );
				this.listenTo( this.model, 'change:et_pb_disabled', this.toggleDisabledClass );
			},

			render : function() {
				this.$el.html( this.template( this.model.toJSON() ) );

				if ( this.model.get( 'et_pb_specialty' ) === 'on' ) {
					this.$el.addClass( 'et_pb_section_specialty' );

					if ( this.model.get( 'et_pb_specialty_placeholder' ) === 'true' ) {
						this.$el.addClass( 'et_pb_section_placeholder' );
					}
				}

				if ( this.model.get( 'et_pb_specialty' ) === 'on' || this.model.get( 'et_pb_fullwidth' ) === 'on' ) {
					this.$el.find( '.et-pb-insert-row' ).remove();
				}

				if ( typeof this.model.get( 'et_pb_global_module' ) !== 'undefined' || ( typeof this.model.get( 'et_pb_template_type' ) !== 'undefined' && 'section' === this.model.get( 'et_pb_template_type' ) && 'global' === et_pb_options.is_global_template ) ) {
					this.$el.addClass( 'et_pb_global' );
				}

				if ( typeof this.model.get( 'et_pb_disabled' ) !== 'undefined' && this.model.get( 'et_pb_disabled' ) === 'on' ) {
					this.$el.addClass( 'et_pb_disabled' );
				}

				if ( typeof this.model.get( 'et_pb_locked' ) !== 'undefined' && this.model.get( 'et_pb_locked' ) === 'on' ) {
					this.$el.addClass( 'et_pb_locked' );
				}

				if ( typeof this.model.get( 'et_pb_collapsed' ) !== 'undefined' && this.model.get( 'et_pb_collapsed' ) === 'on' ) {
					this.$el.addClass( 'et_pb_collapsed' );
				}

				if ( typeof this.model.get( 'pasted_module' ) !== 'undefined' && this.model.get( 'pasted_module' ) ) {
					et_pb_handle_clone_class( this.$el );
				}

				if ( ! _.isUndefined( this.model.get( 'et_pb_temp_global_module' ) ) ) {
					this.$el.addClass( 'et_pb_global_temp' );
				}

				// Split Testing related class
				if ( ET_PageBuilder_AB_Testing.is_active() ) {
					if ( ET_PageBuilder_AB_Testing.is_subject( this.model ) ) {
						this.$el.addClass( 'et_pb_ab_subject' );

						// Apply subject rank coloring
						ET_PageBuilder_AB_Testing.set_subject_rank_coloring( this );
					}

					if ( ET_PageBuilder_AB_Testing.is_goal( this.model ) ) {
						this.$el.addClass( 'et_pb_ab_goal' );
					}

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'section' ) ) {
						this.$el.addClass( 'et_pb_ab_no_permission' );
					}
				}

				this.makeRowsSortable();

				return this;
			},

			showSettings : function( event ) {
				var that = this,
					$current_target = typeof event !== 'undefined' ? $( event.currentTarget ) : '',
					modal_view,
					view_settings = {
						model : this.model,
						collection : this.collection,
						attributes : {
							'data-open_view' : 'module_settings'
						},
						triggered_by_right_click : this.triggered_by_right_click,
						do_preview : this.do_preview
					};

				if ( typeof event !== 'undefined' ) {
					event.preventDefault();
				}

				if ( this.isSectionLocked() ) {
					return;
				}

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'section' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}
				}

				if ( '' !== $current_target && $current_target.closest( '.et_pb_section_specialty' ).length ) {
					var $specialty_section_columns = $current_target.closest( '.et_pb_section_specialty' ).find( '.et-pb-section-content > .et-pb-column' ),
						columns_layout = '';

					if ( $specialty_section_columns.length ) {
						$specialty_section_columns.each( function() {
							columns_layout += '' === columns_layout ? '1_1' : ',1_1';
						});
					}

					view_settings.model.attributes.columns_layout = columns_layout;

				}

				modal_view = new ET_PageBuilder.ModalView( view_settings );

				et_modal_view_rendered = modal_view.render();

				if ( false === et_modal_view_rendered ) {
					et_builder_load_backbone_templates( true );

					setTimeout( function() {
						that.showSettings();
					}, 500 );

					ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

					return;
				}

				ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

				$('body').append( et_modal_view_rendered.el );

				if ( ( typeof modal_view.model.get( 'et_pb_global_module' ) !== 'undefined' && '' !== modal_view.model.get( 'et_pb_global_module' ) ) || ( typeof this.model.get( 'et_pb_template_type' ) !== 'undefined' && 'section' === this.model.get( 'et_pb_template_type' ) && 'global' === et_pb_options.is_global_template ) ) {
					$( '.et_pb_modal_settings_container' ).addClass( 'et_pb_saved_global_modal' );
				}

				if ( typeof this.model.get( 'et_pb_specialty' ) !== 'undefined' && 'on' === this.model.get( 'et_pb_specialty' ) ) {
					$( '.et_pb_modal_settings_container' ).addClass( 'et_pb_specialty_section_settings' );
				}

				et_pb_open_current_tab();
			},

			addSection : function( event ) {
				var module_id = ET_PageBuilder_Layout.generateNewId();

				event.preventDefault();

				et_pb_close_all_right_click_options();

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'added', 'section' );

				this.collection.add( [ {
					type : 'section',
					module_type : 'section',
					et_pb_fullwidth : 'off',
					et_pb_specialty : 'off',
					cid : module_id,
					view : this,
					created : 'auto',
					admin_label : et_pb_options.noun['section']
				} ] );
			},

			addFullwidthSection : function( event ) {
				var module_id = ET_PageBuilder_Layout.generateNewId();

				event.preventDefault();

				et_pb_close_all_right_click_options();

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'added', 'fullwidth_section' );

				this.collection.add( [ {
					type : 'section',
					module_type : 'section',
					et_pb_fullwidth : 'on',
					et_pb_specialty : 'off',
					cid : module_id,
					view : this,
					created : 'auto',
					admin_label : et_pb_options.noun['section']
				} ] );
			},

			addSpecialtySection : function( event ) {
				var module_id = ET_PageBuilder_Layout.generateNewId(),
					$event_target = $(event.target),
					template_type = typeof $event_target !== 'undefined' && typeof $event_target.data( 'is_template' ) !== 'undefined' ? 'section' : '';

				event.preventDefault();

				et_pb_close_all_right_click_options();

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'added', 'specialty_section' );

				this.collection.add( [ {
					type : 'section',
					module_type : 'section',
					et_pb_fullwidth : 'off',
					et_pb_specialty : 'on',
					cid : module_id,
					template_type : template_type,
					view : this,
					created : 'auto',
					admin_label : et_pb_options.noun['section']
				} ] );
			},

			addSavedSection : function( event ) {
				var parent_cid = this.model.get( 'cid' ),
					view_settings = {
						attributes : {
							'data-open_view' : 'saved_templates',
							'data-parent_cid' : parent_cid
						},
						view : this
					},
					main_view = new ET_PageBuilder.ModalView( view_settings );

				et_pb_close_all_right_click_options();

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				$( 'body' ).append( main_view.render().el );

				generate_templates_view( 'include_global', '', 'section', $( '.et-pb-saved-modules-tab' ), 'regular', 0, 'all' );

				event.preventDefault();
			},

			expandSection : function( event ) {
				event.preventDefault();

				var $parent = this.$el.closest('.et_pb_section');

				$parent.removeClass('et_pb_collapsed');

				// Add attribute to shortcode
				this.options.model.attributes.et_pb_collapsed = 'off';

				// Carousel effect for split testing subject
				if ( ET_PageBuilder_AB_Testing.is_active() && this.model.get( 'et_pb_ab_subject' ) === 'on' ) {
					ET_PageBuilder_AB_Testing.subject_carousel( this.model.get( 'cid' ) );
				}

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'expanded', 'section' );

				// Rebuild shortcodes
				ET_PageBuilder_App.saveAsShortcode();
			},

			unlockSection : function( event ) {
				event.preventDefault();

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				var this_el = this,
					$parent = this_el.$el.closest('.et_pb_section'),
					request = et_pb_user_lock_permissions(),
					children_views;

				request.done( function ( response ) {
					if ( true === response ) {
						$parent.removeClass('et_pb_locked');

						// Add attribute to shortcode
						this_el.options.model.attributes.et_pb_locked = 'off';

						children_views = ET_PageBuilder_Layout.getChildrenViews( this_el.model.get('cid') );

						_.each( children_views, function( view, key ) {
							view.$el.removeClass('et_pb_parent_locked');
							view.model.set( 'et_pb_parent_locked', 'off', { silent : true } );
						} );

						// Enable history saving and set meta for history
						ET_PageBuilder_App.allowHistorySaving( 'unlocked', 'section' );

						// Rebuild shortcodes
						ET_PageBuilder_App.saveAsShortcode();
					} else {
						alert( et_pb_options.locked_section_permission_alert );
					}
				});
			},

			addFirstRow: function() {
				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				var module_id = ET_PageBuilder_Layout.generateNewId(),
					global_parent = typeof this.model.get( 'et_pb_global_module' ) !== 'undefined' && '' !== this.model.get( 'et_pb_global_module' ) ? this.model.get( 'et_pb_global_module' ) : '',
					global_parent_cid = '' !== global_parent ? this.model.get( 'cid' ) : '',
					new_row_view;

				this.collection.add( [ {
					type : 'row',
					module_type : 'row',
					cid : module_id,
					parent : this.model.get( 'cid' ),
					view : this,
					et_pb_global_parent : global_parent,
					global_parent_cid : global_parent_cid,
					admin_label : et_pb_options.noun['row']
				} ] );
				new_row_view = ET_PageBuilder_Layout.getView( module_id );
				new_row_view.displayColumnsOptions();
			},

			addRow : function( appendAfter ) {
				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				var module_id = ET_PageBuilder_Layout.generateNewId(),
					global_parent = typeof this.model.get( 'et_pb_global_module' ) !== 'undefined' && '' !== this.model.get( 'et_pb_global_module' ) ? this.model.get( 'et_pb_global_module' ) : '',
					global_parent_cid = '' !== global_parent ? this.model.get( 'cid' ) : '',
					new_row_view;

				this.collection.add( [ {
					type : 'row',
					module_type : 'row',
					cid : module_id,
					parent : this.model.get( 'cid' ),
					view : this,
					appendAfter : appendAfter,
					et_pb_global_parent : global_parent,
					global_parent_cid : global_parent_cid,
					admin_label : et_pb_options.noun['row']
				} ] );
				new_row_view = ET_PageBuilder_Layout.getView( module_id );
				new_row_view.displayColumnsOptions();
			},

			cloneSection : function( event ) {
				event.preventDefault();

				if ( this.isSectionLocked() ) {
					return;
				}

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'section' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}

					if ( ET_PageBuilder_AB_Testing.has_goal( this.model ) && ! ET_PageBuilder_AB_Testing.is_subject( this.model ) ) {
						ET_PageBuilder_AB_Testing.alert( 'cannot_clone_section_has_goal' );
						return;
					}
				}

				var $cloned_element = this.$el.clone(),
					content,
					clone_section,
					view_settings = {
						model      : this.model,
						view       : this.$el,
						view_event : event
					};

				clone_section = new ET_PageBuilder.RightClickOptionsView( view_settings, true );

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'cloned', 'section' );

				clone_section.copy( event );

				clone_section.pasteAfter( event );
			},

			makeRowsSortable : function() {
				var this_el = this,
					sortable_el = this_el.model.get( 'et_pb_fullwidth' ) !== 'on'
						? '.et-pb-section-content'
						: '.et_pb_fullwidth_sortable_area',
					connectWith = ':not(.et_pb_locked) > ' + sortable_el;

				if ( this_el.model.get( 'et_pb_specialty' ) === 'on' ) {
					return;
				}

				// Split Testing adjustment
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Disable sortable of Split testing item for user with no ab_testing permission
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'section' ) ) {
						return;
					}
				}

				this_el.$el.find( sortable_el ).sortable( {
					connectWith: connectWith,
					delay: 100,
					cancel : '.et-pb-settings, .et-pb-clone, .et-pb-remove, .et-pb-row-add, .et-pb-insert-module, .et-pb-insert-column, .et-pb-insert-row, .et_pb_locked, .et-pb-disable-sort',
					update : function( event, ui ) {
						var $sortable_el = this_el.$el.find( sortable_el );

						// Loading process occurs. Dragging is temporarily disabled
						if ( ET_PageBuilder_App.isLoading ) {
							$sortable_el.sortable('cancel');
							return;
						}

						// Split Testing adjustment
						if ( ET_PageBuilder_AB_Testing.is_active() ) {
							// Check for permission user first
							if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( $( ui.item ).children('.et-pb-row-content').attr( 'data-cid' ), 'row' ) ) {
								ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
								$sortable_el.sortable('cancel');
								et_reinitialize_builder_layout();
								return;
							} else {
								// User has proper permission. Verify whether the action is permissible or not
								// IMPORTANT: update event is fired twice, once when the module is moved from its origin and once when the
								// module is landed on its destination. This causes two different way in deciding $sender and $target
								var $item       = $( ui.item ),
									$sender    = _.isEmpty( $( ui.sender ) ) ? $( event.target ).parents('.et_pb_section')  : $( ui.sender ).parents('.et_pb_section'),
									$target    = _.isEmpty( $( ui.sender ) ) ? $( event.toElement ).parents('.et_pb_section') : $( event.target ).parents('.et_pb_section'),
									is_subject  = $item.hasClass('et_pb_ab_subject'),
									is_goal     = $item.hasClass('et_pb_ab_goal'),
									has_subject = $item.find('.et_pb_ab_subject').length,
									has_goal    = $item.find('.et_pb_ab_goal').length,
									is_sender_inside_subject = $sender.closest('.et_pb_ab_subject').length,
									is_target_inside_subject = $target.closest('.et_pb_ab_subject').length,
									is_target_inside_goal = $target.closest('.et_pb_ab_goal').length;

								// Row is goal, being moved to subject-section
								if ( is_goal && ! is_subject && is_target_inside_subject ) {
									ET_PageBuilder_AB_Testing.alert( 'cannot_move_goal_into_subject');
									$sortable_el.sortable('cancel');
									et_reinitialize_builder_layout();
									return;
								}

								// Row has goal, being moved to subject-section
								if ( has_goal && is_target_inside_subject ) {
									ET_PageBuilder_AB_Testing.alert( 'cannot_move_goal_into_subject' );
									$sortable_el.sortable('cancel');
									et_reinitialize_builder_layout();
									return;
								}

								// Row is subject, being moved to goal-section
								if ( is_subject && ! is_goal && is_target_inside_goal ) {
									ET_PageBuilder_AB_Testing.alert( 'cannot_move_subject_into_goal');
									$sortable_el.sortable('cancel');
									et_reinitialize_builder_layout();
									return;
								}

								// Row has subject, being moved to goal-section
								if ( has_subject && is_target_inside_goal ) {
									ET_PageBuilder_AB_Testing.alert( 'cannot_move_subject_into_goal');
									$sortable_el.sortable('cancel');
									et_reinitialize_builder_layout();
									return;
								}

								// Row is a goal inside subject, being moved to anywhere
								if ( is_goal && is_sender_inside_subject ) {
									ET_PageBuilder_AB_Testing.alert( 'cannot_move_row_goal_out_from_subject');
									$sortable_el.sortable('cancel');
									et_reinitialize_builder_layout();
								}
							}
						}

						if ( ! $( ui.item ).closest( event.target ).length ) {

							// don't allow to move the row to another section if the section has only one row
							if ( ! $( event.target ).find( '.et_pb_row' ).length ) {
								$(this).sortable( 'cancel' );
								alert( et_pb_options.section_only_row_dragged_away );
							}

							// do not allow to drag rows into sections where sorting is disabled
							if ( $( ui.item ).closest( '.et-pb-disable-sort').length ) {
								$( event.target ).sortable( 'cancel' );
							}
							// makes sure the code runs one time, if row is dragged into another section
							return;

						}

						var module_cid = $( ui.item ).find( '.et-pb-row-content' ).data( 'cid' );
						var model = this_el.collection.find( function( model ) {
							return model.get('cid') == module_cid;
						} );

						if ( $( ui.item ).closest( '.et_pb_section.et_pb_global' ).length && $( ui.item ).hasClass( 'et_pb_global' ) ) {
							$( ui.sender ).sortable( 'cancel' );
							alert( et_pb_options.global_row_alert );
						} else if ( ( $( ui.item ).closest( '.et_pb_section.et_pb_global' ).length || $( ui.sender ).closest( '.et_pb_section.et_pb_global' ).length ) && '' === et_pb_options.template_post_id ) {
							var	global_module_cid,
								$moving_from,
								$moving_to;

							$moving_from = $( ui.sender ).closest( '.et_pb_section.et_pb_global' );
							$moving_to = $( ui.item ).closest( '.et_pb_section.et_pb_global' );

							if ( $moving_from === $moving_to ) {
								global_module_cid = model.get( 'global_parent_cid' );

								et_pb_update_global_template( global_module_cid );
								et_reinitialize_builder_layout();
							} else {
								var $global_element = $moving_from;

								// remove global parent attributes if moved not to global parent.
								if ( $moving_to.length === 0 && ( ( ! _.isUndefined( model.get( 'et_pb_global_parent' ) ) && '' !== model.get( 'et_pb_global_parent' ) ) || ! _.isUndefined( model.get( 'global_parent_cid' ) ) ) ) {
									model.unset( 'et_pb_global_parent' );
									model.unset( 'global_parent_cid' );
									// remove global attributes from all the child components
									ET_PageBuilder_Layout.removeGlobalAttributes( ET_PageBuilder_Layout.getView( model.get( 'cid' ) ) );
								}

								for ( var i = 1; i <= 2; i++ ) {
									global_module_cid = $global_element.find( '.et-pb-section-content' ).data( 'cid' );

									if ( typeof global_module_cid !== 'undefined' && '' !== global_module_cid ) {

										et_pb_update_global_template( global_module_cid );
										et_reinitialize_builder_layout();
									}

									$global_element = $moving_to;
								}
							}
						}

						ET_PageBuilder_Layout.setNewParentID( ui.item.find( '.et-pb-row-content' ).data( 'cid' ), this_el.model.attributes.cid );


						// Enable history saving and set meta for history
						ET_PageBuilder_App.allowHistorySaving( 'moved', 'row' );

						ET_PageBuilder_Events.trigger( 'et-sortable:update' );

						// Prepare collection sorting based on layout position
						var section_cid       = parseInt( $(this).attr( 'data-cid') ),
							sibling_row_index = 0;

						// Loop row block based on DOM position to ensure its index order
						$(this).find('.et-pb-row-content').each(function(){
							sibling_row_index++;

							var sibling_row_cid = parseInt( $(this).data('cid') ),
								layout_index    = section_cid + sibling_row_index,
								sibling_model   = ET_PageBuilder_Modules.findWhere({ cid : sibling_row_cid });

							// Set layout_index
							sibling_model.set({ layout_index : layout_index });
						});

						// Sort collection based on layout_index
						ET_PageBuilder_Modules.comparator = 'layout_index';
						ET_PageBuilder_Modules.sort();
					},
					start : function( event, ui ) {
						et_pb_close_all_right_click_options();

						// copy row if Alt key pressed
						if ( event.altKey ) {
							var movedRow = ET_PageBuilder_Layout.getView( $( ui.item ).children('.et-pb-row-content').data( 'cid' ) );
							var view_settings = {
								model      : movedRow.model,
								view       : movedRow.$el,
								view_event : event
							};
							var clone_row = new ET_PageBuilder.RightClickOptionsView( view_settings, true );

							clone_row.copy( event, true );

							clone_row.pasteAfter( event, undefined, undefined, undefined, true, true );

							// Enable history saving and set meta for history
							ET_PageBuilder_App.allowHistorySaving( 'cloned', 'row' );
						}
					}
				} );
			},

			addChildView : function( view ) {
				this.child_views.push( view );
			},

			removeChildViews : function() {
				var child_views = ET_PageBuilder_Layout.getChildViews( this.model.attributes.cid );

				_.each( child_views, function( view ) {
					if ( typeof view.model !== 'undefined' )
						view.model.destroy();

					view.remove();
				} );
			},

			removeSection : function( event, remove_all ) {
				var rows,
					remove_last_specialty_section = false;

				if ( event ) event.preventDefault();

				if ( this.isSectionLocked() || ET_PageBuilder_Layout.isChildrenLocked( this.model.get( 'cid' ) ) ) {
					return;
				}

				if ( ET_PageBuilder_App.isLoading && _.isUndefined( remove_all ) && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() && _.isUndefined( remove_all ) && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'section' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}

					if ( ET_PageBuilder_AB_Testing.is_unremovable_subject( this.model ) && _.isUndefined( remove_all ) && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
						return;
					}

					if ( ET_PageBuilder_AB_Testing.has_goal( this.model ) && ! ET_PageBuilder_AB_Testing.is_subject( this.model ) && _.isUndefined( remove_all ) && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'cannot_remove_section_has_goal' );
						return;
					}

					if ( ET_PageBuilder_AB_Testing.has_unremovable_subject( this.model ) && _.isUndefined( remove_all ) && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'cannot_remove_section_has_unremovable_subject' );
						return;
					}
				}

				if ( this.model.get( 'et_pb_fullwidth' ) === 'on' ) {
					this.removeChildViews();
				} else {
					rows = ET_PageBuilder_Layout.getChildViews( this.model.get('cid') );

					_.each( rows, function( row ) {
						if ( row.model.get( 'type' ) === 'column' ) {
							// remove column in specialty section
							row.removeColumn();
						} else {
							row.removeRow( false, true );
						}
					} );
				}

				// the only section left is specialty or fullwidth section
				if ( ! ET_PageBuilder_Layout.get( 'forceRemove' ) && ( this.model.get( 'et_pb_specialty' ) === 'on' || this.model.get( 'et_pb_fullwidth' ) === 'on' ) && ET_PageBuilder_Layout.getNumberOfModules( 'section' ) === 1 ) {
					remove_last_specialty_section = true;
				}

				// if there is only one section, don't remove it
				// allow to remove all sections if removeSection function is called directly
				// remove the specialty section even if it's the last one on the page
				if ( ET_PageBuilder_Layout.get( 'forceRemove' ) || remove_last_specialty_section || ET_PageBuilder_Layout.getNumberOfModules( 'section' ) > 1 ) {
					this.model.destroy();

					ET_PageBuilder_Layout.removeView( this.model.get('cid') );

					this.remove();
				}

				// start with the clean layout if the user removed the last specialty section on the page
				if ( remove_last_specialty_section ) {
					ET_PageBuilder_App.removeAllSections( true );

					return;
				}

				// Enable history saving and set meta for history
				if ( _.isUndefined( remove_all ) ) {
					ET_PageBuilder_App.allowHistorySaving( 'removed', 'section' );
				} else {
					ET_PageBuilder_App.allowHistorySaving( 'cleared', 'layout' );
				}

				// trigger remove event if the row was removed manually ( using a button )
				if ( event ) {
					ET_PageBuilder_Events.trigger( 'et-module:removed' );
				}

				// Run Split Testing updater
				ET_PageBuilder_AB_Testing.update();
			},

			isSectionLocked : function() {
				if ( 'on' === this.model.get( 'et_pb_locked' ) ) {
					return true;
				}

				return false;
			},

			showRightClickOptions : function( event ) {
				event.preventDefault();

				var et_right_click_options_view,
					view_settings = {
						model      : this.model,
						view       : this.$el,
						view_event : event
					};

				et_right_click_options_view = new ET_PageBuilder.RightClickOptionsView( view_settings );
			},

			hideRightClickOptions : function( event ) {
				event.preventDefault();

				et_pb_close_all_right_click_options();
			},

			renameModule : function() {
				this.$( '.et-pb-section-title' ).html( this.model.get( 'admin_label' ) );
			},

			toggleDisabledClass : function() {
				if ( typeof this.model.get( 'et_pb_disabled' ) !== 'undefined' && 'on' === this.model.get( 'et_pb_disabled' ) ) {
					this.$el.addClass( 'et_pb_disabled' );
				} else {
					this.$el.removeClass( 'et_pb_disabled' );
				}
			},

			setABTesting : function ( event ) {
				event.preventDefault();
				event.stopPropagation();

				ET_PageBuilder_AB_Testing.set( this, event );
			}
		} );

		ET_PageBuilder.RowView = window.wp.Backbone.View.extend( {
			className : 'et_pb_row',

			template : _.template( $('#et-builder-row-template').html() ),

			events : {
				'click .et-pb-settings-row' : 'showSettings',
				'click .et-pb-insert-column' : 'displayColumnsOptions',
				'click .et-pb-clone-row' : 'cloneRow',
				'click .et-pb-row-add' : 'addNewRow',
				'click .et-pb-remove-row' : 'removeRow',
				'click .et-pb-change-structure' : 'changeStructure',
				'click .et-pb-expand' : 'expandRow',
				'contextmenu .et-pb-row-add' : 'showRightClickOptions',
				'click.et_pb_row > .et-pb-controls .et-pb-unlock' : 'unlockRow',
				'contextmenu.et_pb_row > .et-pb-controls' : 'showRightClickOptions',
				'contextmenu.et_pb_row > .et-pb-right-click-trigger-overlay' : 'showRightClickOptions',
				'contextmenu .et-pb-column' : 'showRightClickOptions',
				'click.et_pb_row > .et-pb-controls' : 'hideRightClickOptions',
				'click.et_pb_row > .et-pb-right-click-trigger-overlay' : 'hideRightClickOptions',
				'click > .et-pb-locked-overlay' : 'showRightClickOptions',
				'contextmenu > .et-pb-locked-overlay' : 'showRightClickOptions',
				'click' : 'setABTesting',
			},

			initialize : function() {
				this.listenTo( ET_PageBuilder_Events, 'et-add:columns', this.toggleInsertColumnButton );
				this.listenTo( this.model, 'change:admin_label', this.renameModule );
				this.listenTo( this.model, 'change:et_pb_disabled', this.toggleDisabledClass );
			},

			render : function() {
				var parent_views = ET_PageBuilder_Layout.getParentViews( this.model.get( 'parent' ) );

				if ( typeof this.model.get( 'view' ) !== 'undefined' && typeof this.model.get( 'view' ).model.get( 'layout_specialty' ) !== 'undefined' ) {
					this.model.set( 'specialty_row', '1', { silent : true } );
				}

				this.$el.html( this.template( this.model.toJSON() ) );

				if ( typeof this.model.get( 'et_pb_global_module' ) !== 'undefined' || ( typeof this.model.get( 'et_pb_template_type' ) !== 'undefined' && 'row' === this.model.get( 'et_pb_template_type' ) && 'global' === et_pb_options.is_global_template ) ) {
					this.$el.addClass( 'et_pb_global' );
				}

				if ( typeof this.model.get( 'et_pb_disabled' ) !== 'undefined' && this.model.get( 'et_pb_disabled' ) === 'on' ) {
					this.$el.addClass( 'et_pb_disabled' );
				}

				if ( typeof this.model.get( 'et_pb_locked' ) !== 'undefined' && this.model.get( 'et_pb_locked' ) === 'on' ) {
					this.$el.addClass( 'et_pb_locked' );

					_.each( parent_views, function( parent ) {
						parent.$el.addClass( 'et_pb_children_locked' );
					} );
				}

				if ( typeof this.model.get( 'et_pb_parent_locked' ) !== 'undefined' && this.model.get( 'et_pb_parent_locked' ) === 'on' ) {
					this.$el.addClass( 'et_pb_parent_locked' );
				}

				if ( typeof this.model.get( 'et_pb_collapsed' ) !== 'undefined' && this.model.get( 'et_pb_collapsed' ) === 'on' ) {
					this.$el.addClass( 'et_pb_collapsed' );
				}

				if ( typeof this.model.get( 'pasted_module' ) !== 'undefined' && this.model.get( 'pasted_module' ) ) {
					et_pb_handle_clone_class( this.$el );
				}

				if ( ET_PageBuilder_Layout.is_temp_global( this.model ) ) {
					this.$el.addClass( 'et_pb_global_temp' );
				}

				// Split Testing related class
				if ( ET_PageBuilder_AB_Testing.is_active() ) {
					if ( ET_PageBuilder_AB_Testing.is_subject( this.model ) ) {
						this.$el.addClass( 'et_pb_ab_subject' );

						// Apply subject rank coloring
						ET_PageBuilder_AB_Testing.set_subject_rank_coloring( this );
					}

					if ( ET_PageBuilder_AB_Testing.is_goal( this.model ) ) {
						this.$el.addClass( 'et_pb_ab_goal' );
					}

					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'row' ) ) {
						this.$el.addClass( 'et_pb_ab_no_permission' );
					}
				}

				return this;
			},

			showSettings : function( event ) {
				var that = this,
					modal_view,
					view_settings = {
						model : this.model,
						collection : this.collection,
						attributes : {
							'data-open_view' : 'module_settings'
						},
						triggered_by_right_click : this.triggered_by_right_click,
						do_preview : this.do_preview
					};

				if ( typeof event !== 'undefined' ) {
					event.preventDefault();
				}

				if ( this.isRowLocked() ) {
					return;
				}

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'row' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}
				}

				modal_view = new ET_PageBuilder.ModalView( view_settings );

				et_modal_view_rendered = modal_view.render();

				if ( false === et_modal_view_rendered ) {
					et_builder_load_backbone_templates( true );

					setTimeout( function() {
						that.showSettings();
					}, 500 );

					ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

					return;
				}

				ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );


				$('body').append( et_modal_view_rendered.el );

				if ( ( typeof modal_view.model.get( 'et_pb_global_module' ) !== 'undefined' && '' !== modal_view.model.get( 'et_pb_global_module' ) ) || ( ET_PageBuilder_Layout.getView( modal_view.model.get('cid') ).$el.closest( '.et_pb_global' ).length ) || ( typeof this.model.get( 'et_pb_template_type' ) !== 'undefined' && 'row' === this.model.get( 'et_pb_template_type' ) && 'global' === et_pb_options.is_global_template ) ) {
					$( '.et_pb_modal_settings_container' ).addClass( 'et_pb_saved_global_modal' );
				}
			},

			displayColumnsOptions : function( event ) {
				if ( event ) {
					event.preventDefault();
				}

				if ( this.isRowLocked() ) {
					return;
				}

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				var view,
					this_view = this;

				this.model.set( 'open_view', 'column_settings', { silent : true } );

				view = new ET_PageBuilder.ModalView( {
					model : this.model,
					collection : this.collection,
					attributes : {
						'data-open_view' : 'column_settings'
					},
					view : this_view
				} );

				$('body').append( view.render().el );

				this.toggleInsertColumnButton();
			},

			changeStructure : function( event ) {
				event.preventDefault();

				var view,
					this_view = this;

				if ( this.isRowLocked() ) {
					return;
				}

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'row' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}
				}

				this.model.set( 'change_structure', 'true', { silent : true } );

				this.model.set( 'open_view', 'column_settings', { silent : true } );

				ET_PageBuilder.Events = ET_PageBuilder_Events;
				view = new ET_PageBuilder.ModalView( {
					model : this.model,
					collection : this.collection,
					attributes : {
						'data-open_view' : 'column_settings'
					},
					view : this_view
				} );

				$('body').append( view.render().el );
			},

			expandRow : function( event ) {
				event.preventDefault();

				var $parent = this.$el.closest('.et_pb_row');

				$parent.removeClass('et_pb_collapsed');

				// Add attribute to shortcode
				this.options.model.attributes.et_pb_collapsed = 'off';

				// Carousel effect for split testing subject
				if ( ET_PageBuilder_AB_Testing.is_active() && this.model.get( 'et_pb_ab_subject' ) === 'on' ) {
					ET_PageBuilder_AB_Testing.subject_carousel( this.model.get( 'cid' ) );
				}

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'expanded', 'row' );

				// Rebuild shortcodes
				ET_PageBuilder_App.saveAsShortcode();
			},

			unlockRow : function( event ) {
				event.preventDefault();

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				var this_el = this,
					$parent = this_el.$el.closest('.et_pb_row'),
					request = et_pb_user_lock_permissions(),
					children_views,
					parent_views;

				request.done( function ( response ) {
					if ( true === response ) {
						$parent.removeClass('et_pb_locked');

						// Add attribute to shortcode
						this_el.options.model.attributes.et_pb_locked = 'off';

						children_views = ET_PageBuilder_Layout.getChildrenViews( this_el.model.get('cid') );

						_.each( children_views, function( view, key ) {
							view.$el.removeClass('et_pb_parent_locked');
							view.model.set( 'et_pb_parent_locked', 'off', { silent : true } );
						} );

						parent_views = ET_PageBuilder_Layout.getParentViews( this_el.model.get('parent') );

						_.each( parent_views, function( view, key ) {
							if ( ! ET_PageBuilder_Layout.isChildrenLocked( view.model.get( 'cid' ) ) ) {
								view.$el.removeClass('et_pb_children_locked');
							}
						} );

						// Enable history saving and set meta for history
						ET_PageBuilder_App.allowHistorySaving( 'unlocked', 'row' );

						// Rebuild shortcodes
						ET_PageBuilder_App.saveAsShortcode();
					} else {
						alert( et_pb_options.locked_row_permission_alert );
					}
				});
			},

			toggleInsertColumnButton : function() {
				// Manually added row inner (ie empty specialty section's specialty column) has no model
				if (typeof this.model === 'undefined') {
					return;
				}

				var model_id = this.model.get( 'cid' ),
					columnsInRow;

				// check if the current row has at least one column
				columnsInRow = this.collection.find( function( model ) {
					return ( model.get( 'type' ) === 'column' || model.get( 'type' ) === 'column_inner' ) && model.get( 'parent' ) === model_id;
				} );

				if ( ! _.isUndefined( columnsInRow ) ) {
					this.$( '.et-pb-insert-column' ).hide();

					// show "change columns structure" icon, if current row's column layout is set
					this.$( '.et-pb-change-structure' ).show();
				}
			},

			addNewRow : function( event ) {
				var $parent_section = this.$el.closest( '.et-pb-section-content' ),
					$current_target = $( event.currentTarget ),
					parent_view_cid = $current_target.closest( '.et-pb-column-specialty' ).length ? $current_target.closest( '.et-pb-column-specialty' ).data( 'cid' ) : $parent_section.data( 'cid' ),
					parent_view = ET_PageBuilder_Layout.getView( parent_view_cid ),
					global_module_cid = '';

				event.preventDefault();

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				et_pb_close_all_right_click_options();

				if ( 'on' === this.model.get( 'et_pb_parent_locked' ) ) {
					return;
				}

				if ( this.$el.closest( '.et_pb_section.et_pb_global' ).length && typeof parent_view.model.get( 'et_pb_template_type' ) === 'undefined' ) {
					global_module_cid = et_pb_get_global_parent_cid( this );
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'add_row' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}
				}

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'added', 'row' );

				parent_view.addRow( this.$el );

				if ( '' !== global_module_cid ) {
					et_pb_update_global_template( global_module_cid );
				}
			},

			cloneRow : function( event ) {
				var global_module_cid = '',
					parent_view = ET_PageBuilder_Layout.getView( this.model.get( 'parent' ) ),
					clone_row,
					view_settings = {
						model      : this.model,
						view       : this.$el,
						view_event : event
					};

				event.preventDefault();

				if ( this.isRowLocked() ) {
					return;
				}

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ) ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}

					// Row with goal (unless the row is subject) cannot be cloned
					if ( ET_PageBuilder_AB_Testing.has_goal( this.model ) && ! ET_PageBuilder_AB_Testing.is_subject( this.model ) ) {
						ET_PageBuilder_AB_Testing.alert( 'cannot_clone_row_has_goal' );
						return;
					}
				}

				if ( this.$el.closest( '.et_pb_section.et_pb_global' ).length && typeof parent_view.model.get( 'et_pb_template_type' ) === 'undefined' ) {
					global_module_cid = et_pb_get_global_parent_cid( this );
				}

				clone_row = new ET_PageBuilder.RightClickOptionsView( view_settings, true );

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'cloned', 'row' );

				clone_row.copy( event );

				clone_row.pasteAfter( event );

				if ( '' !== global_module_cid ) {
					et_pb_update_global_template( global_module_cid );
				}
			},

			removeRow : function( event, force ) {
				var columns,
					global_module_cid = '',
					parent_view = ET_PageBuilder_Layout.getView( this.model.get( 'parent' ) );

				if ( this.isRowLocked() || ET_PageBuilder_Layout.isChildrenLocked( this.model.get( 'cid' ) ) ) {
					return;
				}

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() && _.isUndefined( force ) && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ) ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}

					if ( ET_PageBuilder_AB_Testing.is_unremovable_subject( this.model ) && _.isUndefined( force ) && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
						return;
					}

					if ( ET_PageBuilder_AB_Testing.has_goal( this.model ) && ! ET_PageBuilder_AB_Testing.is_subject( this.model ) && _.isUndefined( force ) && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'cannot_remove_row_has_goal' );
						return;
					}

					if ( ET_PageBuilder_AB_Testing.has_unremovable_subject( this.model ) && _.isUndefined( force ) && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'cannot_remove_row_has_unremovable_subject' );
						return;
					}
				}

				if ( event ) {
					event.preventDefault();

					// don't allow to remove a specialty section, even if there is only one row in it
					if ( this.$el.closest( '.et-pb-column-specialty' ).length ) {
						event.stopPropagation();
					}

					if ( this.$el.closest( '.et_pb_section.et_pb_global' ).length && typeof parent_view.model.get( 'et_pb_template_type' ) === 'undefined' ) {
						global_module_cid = et_pb_get_global_parent_cid( this );
					}
				}

				columns = ET_PageBuilder_Layout.getChildViews( this.model.get('cid') );

				_.each( columns, function( column ) {
					column.removeColumn();
				} );

				// if there is only one row in the section, don't remove it
				if ( ET_PageBuilder_Layout.get( 'forceRemove' ) || ET_PageBuilder_Layout.getNumberOf( 'row', this.model.get('parent') ) > 1 ) {
					this.model.destroy();

					ET_PageBuilder_Layout.removeView( this.model.get('cid') );

					this.remove();
				} else {
					this.$( '.et-pb-insert-column' ).show();

					// hide "change columns structure" icon, column layout can be re-applied using "Insert column(s)" button
					this.$( '.et-pb-change-structure' ).hide();
				}

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'removed', 'row' );

				// trigger remove event if the row was removed manually ( using a button )
				if ( event ) {
					ET_PageBuilder_Events.trigger( 'et-module:removed' );
				}

				if ( '' !== global_module_cid ) {
					et_pb_update_global_template( global_module_cid );
				}

				// Run Split Testing updater
				ET_PageBuilder_AB_Testing.update();
			},

			isRowLocked : function() {
				if ( 'on' === this.model.get( 'et_pb_locked' ) || 'on' === this.model.get( 'et_pb_parent_locked' ) ) {
					return true;
				}

				return false;
			},

			showRightClickOptions : function( event ) {
				event.preventDefault();
				var $event_target = $( event.target ),
					et_right_click_options_view,
					view_settings;

				// Do nothing if Module or "Insert Module" clicked
				if ( $event_target.closest( '.et-pb-insert-module' ).length || $event_target.closest('.et-pb-insert-row').length || $event_target.hasClass( 'et_pb_module_block' ) || $event_target.closest( '.et_pb_module_block' ).length ) {
					return;
				}

				et_right_click_options_view,
				view_settings = {
					model      : this.model,
					view       : this.$el,
					view_event : event
				};

				et_right_click_options_view = new ET_PageBuilder.RightClickOptionsView( view_settings );
			},

			hideRightClickOptions : function( event ) {
				event.preventDefault();

				et_pb_close_all_right_click_options();
			},

			renameModule : function() {
				this.$( '.et-pb-row-title' ).html( this.model.get( 'admin_label' ) );
			},

			toggleDisabledClass : function() {
				if ( typeof this.model.get( 'et_pb_disabled' ) !== 'undefined' && 'on' === this.model.get( 'et_pb_disabled' ) ) {
					this.$el.addClass( 'et_pb_disabled' );
				} else {
					this.$el.removeClass( 'et_pb_disabled' );
				}
			},

			setABTesting : function ( event ) {
				event.preventDefault();
				event.stopPropagation();

				ET_PageBuilder_AB_Testing.set( this, event );
			}
		} );

		ET_PageBuilder.ModalView = window.wp.Backbone.View.extend( {

			className : 'et_pb_modal_settings_container',

			template : _.template( $('#et-builder-modal-template').html() ),

			events : {
				'click .et-pb-modal-save' : 'saveSettings',
				'click .et-pb-modal-preview-template' : 'preview',
				'click .et-pb-preview-mobile' : 'resizePreviewScreen',
				'click .et-pb-preview-tablet' : 'resizePreviewScreen',
				'click .et-pb-preview-desktop' : 'resizePreviewScreen',
				'click .et-pb-modal-close' : 'closeModal',
				'click .et-pb-modal-save-template' : 'saveTemplate',
				'change #et_pb_select_category' : 'applyFilter'
			},

			initialize : function( attributes ) {
				this.listenTo( ET_PageBuilder_Events, 'et-add:columns', this.removeView );

				// listen to module settings box that is created after the user selects new module to add
				this.listenTo( ET_PageBuilder_Events, 'et-new_module:show_settings', this.removeView );

				this.listenTo( ET_PageBuilder_Events, 'et-saved_layout:loaded', this.removeView );

				this.options = attributes;
			},

			render : function() {
				var view,
					view_settings = {
						model : this.model,
						collection : this.collection,
						view : this.options.view
					},
					fake_value = false;

				this.$el.attr( 'tabindex', 0 ); // set tabindex to make the div focusable

				// update the row view if it has been dragged into another column
				if ( typeof this.model !== 'undefined' && typeof this.model.get( 'view' ) !== 'undefined' && ( this.model.get( 'module_type' ) === 'row_inner' || this.model.get( 'module_type' ) === 'row' ) && this.model.get( 'parent' ) !== this.model.get( 'view' ).$el.data( 'cid' ) ) {
					this.model.set( 'view', ET_PageBuilder_Layout.getView( this.model.get( 'parent' ) ), { silent : true } );
				}

				if ( this.attributes['data-open_view'] === 'all_modules' && this.model.get( 'module_type' ) === 'section' && this.model.get( 'et_pb_fullwidth' ) === 'on' ) {
					this.model.set( 'type', 'column', { silent : true } );
					fake_value = true;
				}

				if ( typeof this.model !== 'undefined' ) {
					var this_parent_view = ET_PageBuilder_Layout.getView( this.model.get( 'parent' ) );

					if ( this.attributes['data-open_view'] === 'column_specialty_settings' ) {
						this.model.set( 'open_view', 'column_specialty_settings', { silent : true } );
					}

					this.$el.html( this.template( this.model.toJSON() ) );

					if ( this.attributes['data-open_view'] === 'column_specialty_settings' ) {
						this.model.unset( 'open_view', 'column_specialty_settings', { silent : true } );
					}
				}
				else
					this.$el.html( this.template() );

				if ( fake_value )
					this.model.set( 'type', 'section', { silent : true } );

				this.container = this.$('.et-pb-modal-container');

				if ( this.attributes['data-open_view'] === 'column_settings' ) {
					view = new ET_PageBuilder.ColumnSettingsView( view_settings );
				} else if ( this.attributes['data-open_view'] === 'all_modules' ) {
					view_settings['attributes'] = {
						'data-parent_cid' : this.model.get( 'cid' )
					}

					view = new ET_PageBuilder.ModulesView( view_settings );
				} else if ( this.attributes['data-open_view'] === 'module_settings' ) {
					var this_module_type = this.model.get( 'module_type' );

					// check the Row parent and if it's inside the column then change the type Row to Row Inner.
					if ( 'row' === this_module_type && ! _.isUndefined( this_parent_view ) && 'column' === this_parent_view.model.get('type') ) {
						this_module_type = 'row_inner';
					}

					view_settings['attributes'] = {
						'data-module_type' : this_module_type
					}

					view_settings['view'] = this;

					view = new ET_PageBuilder.ModuleSettingsView( view_settings );
				} else if ( this.attributes['data-open_view'] === 'save_layout' ) {
					view = new ET_PageBuilder.SaveLayoutSettingsView( view_settings );
				} else if ( this.attributes['data-open_view'] === 'column_specialty_settings' ) {
					view = new ET_PageBuilder.ColumnSettingsView( view_settings );
				} else if ( this.attributes['data-open_view'] === 'saved_templates' ) {
					view = new ET_PageBuilder.TemplatesModal( { attributes: { 'data-parent_cid' : this.attributes['data-parent_cid'] } } );
				} else if ( this.attributes['data-open_view'] === 'help' ) {
					view = new ET_PageBuilder.HelpView();
				}
				// do not proceed and return false if no template for this module exist yet
				if ( typeof view.attributes !== 'undefined' && 'no_template' === view.attributes['data-no_template'] ) {
					return false;
				}

				this.container.append( view.render().el );

				if ( this.attributes['data-open_view'] === 'column_settings' ) {
					// if column settings layout was generated, remove open_view attribute from a row
					// the row module modal window shouldn't have this attribute attached
					this.model.unset( 'open_view', { silent : true } );
				}

				// show only modules that the current element can contain
				if ( this.attributes['data-open_view'] === 'all_modules' ) {
					if ( this.model.get( 'module_type' ) === 'section' && typeof( this.model.get( 'et_pb_fullwidth' ) !== 'undefined' ) && this.model.get( 'et_pb_fullwidth' ) === 'on' ) {
						$( view.render().el ).find( '.et-pb-all-modules li:not(.et_pb_fullwidth_only_module)' ).remove();
					} else {
						$( view.render().el ).find( 'li.et_pb_fullwidth_only_module' ).remove();
					}
				}

				if ( $( '.et_pb_modal_overlay' ).length ) {
					$( '.et_pb_modal_overlay' ).remove();
					$( 'body' ).removeClass( 'et_pb_stop_scroll' );
				}

				if ( $( 'body' ).hasClass( 'et_pb_modal_fade_in' ) ) {
					$( 'body' ).append( '<div class="et_pb_modal_overlay et_pb_no_animation"></div>' );
				} else {
					$( 'body' ).append( '<div class="et_pb_modal_overlay"></div>' );
				}

				$( 'body' ).addClass( 'et_pb_stop_scroll' );

				return this;
			},

			closeModal : function( event ) {
				event.preventDefault();

				if ( $( '.et_modal_on_top' ).length ) {
					$( '.et_modal_on_top' ).remove();
				} else {

					if ( typeof this.model !== 'undefined' && this.model.get( 'type' ) === 'module' && this.$( '#et_pb_content_new' ).length )
						et_pb_tinymce_remove_control( 'et_pb_content_new' );

					et_pb_hide_active_color_picker( this );

					et_pb_close_modal_view( this, 'trigger_event' );
				}
			},

			removeView : function() {
				if ( typeof this.model === 'undefined' || ( this.model.get( 'type' ) === 'row' || this.model.get( 'type' ) === 'column' || this.model.get( 'type' ) === 'row_inner' || this.model.get( 'type' ) === 'column_inner' || ( this.model.get( 'type' ) === 'section' && ( this.model.get( 'et_pb_fullwidth' ) === 'on' || this.model.get( 'et_pb_specialty' ) === 'on' ) ) ) ) {
					if ( typeof this.model !== 'undefined' && typeof this.model.get( 'type' ) !== 'undefined' && ( this.model.get( 'type' ) === 'column' || this.model.get( 'type' ) === 'column_inner' || ( this.model.get( 'type' ) === 'section' &&  this.model.get( 'et_pb_fullwidth' ) === 'on' ) ) ) {
						var that = this,
							$opened_tab = $( that.el ).find( '.et-pb-main-settings.active-container' );

						// if we're adding module from library, then close everything. Otherwise leave overlay in place and add specific classes
						if ( $opened_tab.hasClass( 'et-pb-saved-modules-tab' ) ) {
							et_pb_close_modal_view( that );
						} else {
							that.remove();

							$( 'body' ).addClass( 'et_pb_modal_fade_in' );
							$( '.et_pb_modal_overlay' ).addClass( 'et_pb_no_animation' );
							setTimeout( function() {
								$( '.et_pb_modal_settings_container' ).addClass( 'et_pb_no_animation' );
								$( 'body' ).removeClass( 'et_pb_modal_fade_in' );
							}, 500);
						}
					} else {
						et_pb_close_modal_view( this );
					}
				} else {
					this.removeOverlay();
				}
			},

			saveSettings : function( event, close_modal ) {
				var that = this,
					global_module_cid = '',
					this_view = ET_PageBuilder_Layout.getView( that.model.get( 'cid' ) ),
					this_parent_view = ! _.isUndefined( that.model.get( 'parent' ) ) ? ET_PageBuilder_Layout.getView( that.model.get( 'parent' ) ) : '',
					global_holder_view = this_view,
					update_template_only = false,
					close_modal = _.isUndefined( close_modal ) ? true : close_modal,
					is_disabled = 'not-allowed' === $( event.target ).css( 'cursor' );

				// get the global module ID if exists.
				if ( ! _.isUndefined( global_holder_view.model.get( 'et_pb_global_module' ) )  ) {
					// if the module is global
					global_module_cid = global_holder_view.model.get( 'cid' );
				} else {
					// check all parents to find the global parent if exists
					while ( ! _.isUndefined( global_holder_view.model.get( 'parent' ) ) && '' === global_module_cid ) {
						// Refresh global holder for new loop
						global_holder_view = ET_PageBuilder_Layout.getView( global_holder_view.model.get( 'parent' ) );
						global_module_cid = '' === global_module_cid && ! _.isUndefined( global_holder_view.model.get( 'et_pb_global_module' ) ) ? global_holder_view.model.get( 'cid' ) : global_module_cid;
					}
				}

				event.preventDefault();

				if ( is_disabled ) {
					return;
				}

				// Disabling state and mark it. It takes a while for generating shortcode,
				// so ensure that user doesn't update the page before shortcode generation has completed
				$('#publish').addClass( 'disabled' );

				ET_PageBuilder_App.disable_publish = true;

				if ( ( typeof global_holder_view.model.get( 'global_parent_cid' ) !== 'undefined' && '' !== global_holder_view.model.get( 'global_parent_cid' ) ) || ( typeof global_holder_view.model.get( 'et_pb_global_module' ) !== 'undefined' && '' !== global_holder_view.model.get( 'et_pb_global_module' ) ) ) {
					global_module_cid = typeof global_holder_view.model.get( 'global_parent_cid' ) !== 'undefined' ? global_holder_view.model.get( 'global_parent_cid' ) : global_holder_view.model.get( 'cid' );
				}

				that.performSaving();

				if ( '' !== global_module_cid ) {
					et_pb_update_global_template( global_module_cid );
				}

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'edited', that.model.get( 'type' ), that.model.get( 'admin_label' ) );

				// In some contexts, closing modal view isn't needed & only settings saving needed
				if ( ! close_modal ) {
					return;
				}

				et_pb_tinymce_remove_control( 'et_pb_content_new' );

				et_pb_hide_active_color_picker( that );

				et_pb_close_modal_view( that, 'trigger_event' );

				if ( ET_PageBuilder_AB_Testing.is_active() ) {
					// Update subject rank coloring and subject ID
					ET_PageBuilder_AB_Testing.set_subject_rank_coloring( this_view );
				}
			},

			preview : function( event ) {
				var cid          = this.model.get( 'cid' ) ,
					shortcode,
					$button      = $( event.target ).is( 'a' ) ? $( event.target ) : $( event.target ).parent( 'a' ),
					$container   = $( event.target ).parents( '.et-pb-modal-container' ),
					request_data,
					section_view,
					msie         = document.documentMode;

				event.preventDefault();

				// Save modified settings, if it is necesarry. Direct preview from right click doesn't need to be saved
				if ( _.isUndefined( this.options.triggered_by_right_click ) ) {
					this.saveSettings( event, false );
				} else {
					// Triggered by right click is one time thing. Remove it as soon as it has been used
					delete this.options.triggered_by_right_click;
				}

				if ( ! _.isUndefined( this.options.do_preview ) ) {
					// Do preview is one time thing. Remove it as soon as it has been used
					delete this.options.do_preview;
				}

				if ( et_pb_options.is_divi_library === "1" && $.inArray( et_pb_options.layout_type, [ "row", "module" ] ) > -1 ) {
					// Divi Library's layout editor auto generates section and row in module and row layout type
					// The auto generates item cause cause an issue during shortcode generation
					// Removing its cid will force ET_PageBuilder_App.generateCompleteShortcode to generate the whole page's layout shortcode which solves the preview issue
					cid = undefined;
				} else if ( this.model.get( 'type' ) !== 'section' ) {
					// Module's layout depends on the column it belongs. Hence, always preview the item in context of section
					section_view = ET_PageBuilder_Layout.getSectionView( this.model.get( 'parent' ) );

					if ( ! _.isUndefined( section_view ) ) {
						cid = section_view.model.attributes.cid;
					}
				}

				// Get shortcode based on section's cid
				shortcode = ET_PageBuilder_App.generateCompleteShortcode( cid );

				request_data = {
					et_pb_preview_nonce : et_pb_options.et_pb_preview_nonce,
					shortcode           : shortcode,
					post_title          : $('#title').val()
				};

				// Toggle button state
				$button.toggleClass( 'active' );

				// Toggle container state
				$container.toggleClass( 'et-pb-item-previewing' );

				if ( $button.hasClass( 'active' ) ) {
					// Create the iFrame on the fly. This will speed up modalView init
					var $iframe = $('<iframe />', {
								 	id : 'et-pb-preview-screen',
								 	src : et_pb_options.preview_url + '&et_pb_preview_nonce=' + et_pb_options.et_pb_preview_nonce
								 } ),
						has_render_page = false;

					// Add the iframe into preview tab
					 $('.et-pb-preview-tab' ).html( $iframe );

					 // Pass the item's setup to the screen
					 $('#et-pb-preview-screen').load( function(){
					 	if ( has_render_page ) {
					 		return;
					 	}

					 	// Get iFrame
						preview = document.getElementById( 'et-pb-preview-screen' );

						// IE9 below fix. They have postMessage, but it has to be in string
						if ( ! _.isUndefined( msie ) && msie < 10 ) {
							request_data = JSON.stringify( request_data );
						}

						// Pass shortcode structure to iFrame to be displayed
						preview.contentWindow.postMessage( request_data, et_pb_options.preview_url );

						has_render_page = true;
					 });
				} else {
					$( '.et-pb-preview-tab' ).empty();

					// Reset active state
					$('.et-pb-preview-screensize-switcher a').removeClass( 'active' );

					// Set desktop as active
					$('.et-pb-preview-desktop').addClass( 'active' );
				}
			},

			resizePreviewScreen : function( event ) {
				event.preventDefault();

				var $link = $( event.target ),
					width = _.isUndefined( $link.data( 'width' ) ) ? '100%' : $link.data( 'width' );

				// Reset active state
				$('.et-pb-preview-screensize-switcher a').removeClass( 'active' );

				// Set current as active
				$link.addClass( 'active' );

				// Set iFrame width
				$('#et-pb-preview-screen').animate({
					'width' : width
				});
			},

			getAttr: function( object, name ) {
				return _.isUndefined( object[ name ] ) ? '' : object[ name ];
			},

			performSaving : function( option_tabs_selector ) {
				var thisClass  = this,
					attributes = {},
					defaults   = {},
					options_selector = typeof option_tabs_selector !== 'undefined' && '' !== option_tabs_selector ? option_tabs_selector : 'input, select, textarea, #et_pb_content_main';

				var $et_form_validation;
				$et_form_validation = $(this)[0].$el.find('form.validate');
				if ( $et_form_validation.length ) {
					validator = $et_form_validation.validate();
					if ( !validator.form() ) {
						et_builder_debug_message('failed form validation');
						et_builder_debug_message('failed elements: ');
						et_builder_debug_message( validator.errorList );
						validator.focusInvalid();
						return;
					}
					et_builder_debug_message('passed form validation');
				}

				var global_module_id = 'global' === et_pb_options.is_global_template ? et_pb_options.template_post_id : this.model.get( 'et_pb_global_module' );

				// update the unsynced options for the module
				if ( $( '.et_pb_global_sync_switcher' ).length > 0 && typeof global_module_id !== 'undefined' && '' !== global_module_id ) {
					var unsynced_options_array = [];

					if ( $( '.et_pb_global_unsynced' ).length > 0 ) {
						$( '.et_pb_global_unsynced' ).each( function() {
							var $this_el = $( this );
							var this_option_name = $this_el.data( 'option_name' );

							unsynced_options_array.push( this_option_name );

							// unsync mobile options if exist
							if ( ! _.isUndefined( $this_el.data( 'additional_options' ) ) && 'mobile' === $this_el.data( 'additional_options' ) ) {
								unsynced_options_array.push( this_option_name + '_tablet' );
								unsynced_options_array.push( this_option_name + '_phone' );
							}
						});
					}

					et_pb_all_unsynced_options[ global_module_id ] = unsynced_options_array;

					// update the value in hidden option so unsynced options will be saved on post Update.
					if ( 'global' === et_pb_options.is_global_template && $( '#et_pb_unsynced_global_attrs' ).length !== 0 ) {
						$( '#et_pb_unsynced_global_attrs' ).val( JSON.stringify( unsynced_options_array ) );
					}
				}

				ET_PageBuilder.Events.trigger( 'et-modal-settings:save', this );

				this.$( options_selector ).each( function() {
					var $this_el = $(this),
						setting_value,
						checked_values = [],
						name = $this_el.is('#et_pb_content_main') ? 'et_pb_content_new' : $this_el.attr('id'),
						default_value = $this_el.data('default') || '',
						custom_css_option_value;

					// convert default value to string to make sure current and default values have the same type
					default_value = default_value + '';

					// name attribute is used in normal html checkboxes, use it instead of ID
					if ( $this_el.is( ':checkbox' ) ) {
						name = $this_el.attr('name');
					}

					if ( typeof name === 'undefined' || ( -1 !== name.indexOf( 'qt_' ) && 'button' === $this_el.attr( 'type' ) ) ) {
						// settings should have an ID and shouldn't be a Quick Tag button from the tinyMCE in order to be saved
						return true;
					}

					if ( $this_el.hasClass( 'et-pb-helper-field' ) ) {
						// don't process helper fields
						return true;
					}

					// All checkbox values are saved at once on the next step, so if the attribute name
					// already exists, do nothing
					if ( $this_el.is( ':checkbox' ) && typeof attributes[name] !== 'undefined' ) {
						return true;
					}

					// Validate colorpicker - if invalid color given, return to default color
					if ( $this_el.hasClass( 'et-pb-color-picker-hex' ) && new Color( $this_el.val() ).error && ! $this_el.hasClass( 'et-pb-is-cleared' ) ) {
						$this_el.val( $this_el.data( 'selected-value') );
					}

					// Process all checkboxex for the current setting at once
					if ( $this_el.is( ':checkbox' ) && typeof attributes[name] === 'undefined' ) {
						$this_el.closest( '.et-pb-option-container' ).find( '[name="' + name + '"]:checked' ).each( function() {
							checked_values.push( $(this).val() );
						} );

						setting_value = checked_values.join( "," );
					} else if ( $this_el.is( '#et_pb_content_main' ) ) {
						// Process main content

						setting_value = $this_el.html();

						// Replace temporary ^^ signs with double quotes
						setting_value = setting_value.replace( /\^\^/g, '%22' );
					} else if ( $this_el.closest( '.et-pb-custom-css-option' ).length ) {
						// Custom CSS settings content should be modified before it is added to the shortcode attribute

						custom_css_option_value = $this_el.val();

						// replace new lines with || and backlash \ with %92 in Custom CSS settings
						setting_value = '' !== custom_css_option_value ? custom_css_option_value.replace( /(?:\r\n|\r|\n)/g, '\|\|' ).replace( /\\/g, '%92' ) : '';
					} else if ( $this_el.hasClass( 'et-pb-range-input' ) ) {
						// Get range input value
						setting_value = et_pb_get_range_input_value( $this_el );

						if ( $this_el.hasClass( 'et-pb-validate-unit' ) ) {
							setting_value = et_pb_sanitize_input_unit_value( setting_value.toString(), false, 'no_default_unit' );
						}
					} else if ( $this_el.hasClass( 'et-pb-validate-unit' ) ) {
						// Process validated unit
						setting_value = et_pb_sanitize_input_unit_value( $this_el.val(), false, '' );
					} else if ( ! $this_el.is( ':checkbox' ) ) {
						// Process all other settings: inputs, textarea#et_pb_content_new, range sliders etc.

						setting_value = $this_el.is('textarea#et_pb_content_new')
							? et_pb_get_content( 'et_pb_content_new' )
							: $this_el.val();

						if ( $this_el.hasClass( 'et-pb-range-input' ) && setting_value === 'px' ) {
							setting_value = '';
						}
					}

					// if default value is set, add it to the defaults object
					if ( default_value !== '' ) {
						defaults[ name ] = default_value;
					}

					// save the attribute value
					attributes[name] = setting_value;
				} );

				// add defaults object
				attributes['module_defaults'] = defaults;

				// remove padding_mobile based on last_edited value to remove dependency to padding_mobile and rely on responsive padding
				var module_attributes = this.model.attributes,
					module_previous_attributes = this.model._previousAttributes,
					module_type = module_attributes.type,
					custom_padding_last_edited = _.isUndefined(attributes.et_pb_custom_padding_last_edited) ? [] : attributes.et_pb_custom_padding_last_edited.split('|'),
					responsive_padding_active = typeof custom_padding_last_edited === 'object' && custom_padding_last_edited[0] === 'on',
					is_custom_padding_updated = thisClass.getAttr( attributes, 'et_pb_custom_padding' ) !== thisClass.getAttr( module_previous_attributes, 'et_pb_custom_padding' ),
					is_custom_padding_tablet_updated = responsive_padding_active && thisClass.getAttr( attributes, 'et_pb_custom_padding_tablet' ) !== thisClass.getAttr( module_previous_attributes, 'et_pb_custom_padding_tablet' ),
					is_custom_padding_phone_updated = responsive_padding_active && thisClass.getAttr( attributes, 'et_pb_custom_padding_phone' ) !== thisClass.getAttr( module_previous_attributes, 'et_pb_custom_padding_phone' ),
					is_custom_padding_field_updated = is_custom_padding_updated || is_custom_padding_tablet_updated || is_custom_padding_phone_updated;

				// remove padding_mobile on section if responsive padding is active
				if ( module_type === 'section' && ( responsive_padding_active || is_custom_padding_field_updated ) ) {
					attributes.et_pb_padding_mobile = '';
				}

				if ( module_type === 'row' || module_type === 'row_inner' ) {
					// remove padding_mobile on row if responsive padding is active
					if ( ( responsive_padding_active || is_custom_padding_field_updated ) ) {
						attributes.et_pb_padding_mobile = '';
					}

					// remove column_padding
					var column_count = typeof module_attributes.columns_layout === 'undefined' ? 0 : module_attributes.columns_layout.split(',').length;
					for (var column_index = 1; column_index <= column_count; column_index++) {
						var column_padding_last_edited_value = attributes['et_pb_padding_' + column_index + '_last_edited'],
							column_padding_last_edited = typeof column_padding_last_edited_value === 'undefined' ? [] : column_padding_last_edited_value.split('|'),
							column_responsive_padding_active = typeof column_padding_last_edited === 'object' && column_padding_last_edited[0] === 'on',
							is_column_padding_top_updated    = thisClass.getAttr( attributes, 'et_pb_padding_top_' + column_index ) !== thisClass.getAttr( module_previous_attributes, 'et_pb_padding_top_' + column_index ),
							is_column_padding_right_updated  = thisClass.getAttr( attributes, 'et_pb_padding_right_' + column_index ) !== thisClass.getAttr( module_previous_attributes, 'et_pb_padding_right_' + column_index ),
							is_column_padding_bottom_updated = thisClass.getAttr( attributes, 'et_pb_padding_bottom_' + column_index ) !== thisClass.getAttr( module_previous_attributes, 'et_pb_padding_bottom_' + column_index ),
							is_column_padding_left_updated   = thisClass.getAttr( attributes, 'et_pb_padding_left_' + column_index ) !== thisClass.getAttr( module_previous_attributes, 'et_pb_padding_left_' + column_index ),
							is_column_padding_updated        = is_column_padding_top_updated || is_column_padding_right_updated || is_column_padding_bottom_updated || is_column_padding_left_updated,
							is_column_padding_tablet_updated = column_responsive_padding_active && thisClass.getAttr( attributes, 'et_pb_padding_' + column_index + '_tablet' ) !== thisClass.getAttr( module_previous_attributes, 'et_pb_padding_' + column_index + '_tablet' ),
							is_column_padding_phone_updated  = column_responsive_padding_active && thisClass.getAttr( attributes, 'et_pb_padding_' + column_index + '_phone' ) !== thisClass.getAttr( module_previous_attributes, 'et_pb_padding_' + column_index + '_phone'),
							is_column_padding_field_updated  = is_column_padding_updated || is_column_padding_tablet_updated || is_column_padding_phone_updated;

						if ( column_responsive_padding_active || is_column_padding_field_updated ) {
							attributes.et_pb_column_padding_mobile = '';
							break;
						}
					}
				}

				// set model attributes
				this.model.set( attributes );
			},

			saveTemplate : function( event ) {
				var module_width = -1 !== this.model.get( 'module_type' ).indexOf( 'fullwidth' ) ? 'fullwidth' : 'regular',
					columns_layout = typeof this.model.get( 'columns_layout' ) !== 'undefined' ? this.model.get( 'columns_layout' ) : '0',
					is_disabled = 'not-allowed' === $( event.target ).css( 'cursor' );
				event.preventDefault();

				if ( is_disabled ) {
					return;
				}

				et_pb_create_prompt_modal( 'save_template', this, module_width, columns_layout );
			},

			removeOverlay : function() {
				var $overlay = $( '.et_pb_modal_overlay' );
				if ( $overlay.length ) {

					$overlay.addClass( 'et_pb_overlay_closing' );

					setTimeout( function() {
						$overlay.remove();

						$( 'body' ).removeClass( 'et_pb_stop_scroll' );
					}, 600 );
				}

				// Check for existence of disable_publish element, don't do auto enable publish
				// if not necesarry. Example: opening Modal View, then close it without further action
				if ( ! _.isUndefined( ET_PageBuilder_App.disable_publish ) ) {
					var auto_enable_publishing = setTimeout( function() {

						// Check for disable_publish state, auto enable after three seconds
						// This means no et_pb_set_content triggered
						if ( ! _.isUndefined( ET_PageBuilder_App.disable_publish ) ) {
							$('#publish').removeClass( 'disabled' );

							delete ET_PageBuilder_App.disable_publish;
						}
					}, 3000 );
				}
			},

			applyFilter : function() {
				var $event_target = $(event.target),
					all_data = $event_target.data( 'attr' ),
					selected_category = $event_target.val();
				all_data.append_to.html( '' );
				generate_templates_view( all_data.include_global, '', all_data.layout_type, all_data.append_to, all_data.module_width, all_data.specialty_cols, selected_category );
			}

		} );

		ET_PageBuilder.ColumnView = window.wp.Backbone.View.extend( {
			template : _.template( $('#et-builder-column-template').html() ),
			templateAddRow : _.template( $('#et-builder-specialty-column-template').html() ),

			events : {
				'click .et-pb-insert-module' : 'addModule',
				'click .et-pb-insert-row' : 'addModule',
				'contextmenu > .et-pb-insert-module' : 'showRightClickOptions',
				'click' : 'hideRightClickOptions'
			},

			initialize : function() {
				this.$el.attr( 'data-cid', this.model.get( 'cid' ) );
			},

			render : function() {
				var this_el = this,
					is_fullwidth_section = this.model.get( 'module_type' ) === 'section' && this.model.get( 'et_pb_fullwidth' ) === 'on',
					connect_with = ( ! is_fullwidth_section ? ".et-pb-column:not(.et-pb-column-specialty, .et_pb_parent_locked)" : ".et_pb_fullwidth_sortable_area" );

				this.$el.html( this.template( this.model.toJSON() ) );

				// Specialty section's column button displays add row instead of add module
				if (typeof this.model.attributes.specialty_columns !== 'undefined' ) {
					this.$el.html( this.templateAddRow( this.model.toJSON() ) );
				}

				if ( is_fullwidth_section )
					this.$el.addClass( 'et_pb_fullwidth_sortable_area' );

				if ( this.model.get( 'layout_specialty' ) === '1' ) {
					connect_with = '.et-pb-column-specialty:not(.et_pb_parent_locked)';
				}

				if ( this.model.get( 'created' ) === 'manually' && ! _.isUndefined( this.model.get( 'et_pb_specialty_columns' ) ) ) {
					this.$el.addClass( 'et-pb-column-specialty' );
				}

				if ( this.isColumnParentLocked( this.model.get( 'parent' ) ) ) {
					this.$el.addClass( 'et_pb_parent_locked' );
					this.model.set( 'et_pb_parent_locked', 'on', { silent : true } );
				}

				// Split Testing adjustment
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Disable sortable of Split testing item for user with no ab_testing permission
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'column' ) ) {
						return this;
					}
				}

				this.$el.sortable( {
					cancel : '.et-pb-settings, .et-pb-clone, .et-pb-remove, .et-pb-insert-module, .et-pb-insert-column, .et-pb-insert-row, .et_pb_locked, .et-pb-disable-sort',
					connectWith: connect_with,
					delay: 100,
					items : ( this.model.get( 'layout_specialty' ) !== '1' ? '.et_pb_module_block' : '.et_pb_row' ),
					receive: function(event, ui) {
						var $this = $(this),
							columns_number,
							cancel_action = false;

						if ( $this.hasClass( 'et-pb-column-specialty' ) ) {
							// revert if the last row is being dragged out of the specialty section
							// or the module block is placed directly into the section
							// or 3-column row is placed into the row that can't handle it
							if ( ! $( ui.sender ).find( '.et_pb_row' ).length || $( ui.item ).is( '.et_pb_module_block' ) ) {
								alert( et_pb_options.section_only_row_dragged_away );
								cancel_action = true;
							} else {
								columns_number = $(ui.item).find( '.et-pb-row-container > .et-pb-column' ).length;

								if ( columns_number === 3 && parseInt( ET_PageBuilder_Layout.getView( $this.data( 'cid' ) ).model.get( 'specialty_columns' ) ) !== 3 ) {
									alert( et_pb_options.stop_dropping_3_col_row );
									cancel_action = true;
								}
							}
						}

						// do not allow to drag modules into sections and rows where sorting is disabled
						if ( $( ui.item ).closest( '.et-pb-disable-sort').length ) {
							cancel_action = true;
						}

						if ( ( $( ui.item ).closest( '.et_pb_section.et_pb_global' ).length || $( ui.item ).closest( '.et_pb_row.et_pb_global' ).length ) && $( ui.item ).hasClass( 'et_pb_global' ) ) {
							alert( et_pb_options.global_module_alert );
							cancel_action = true;
						}

						if ( cancel_action ) {
							$(ui.sender).sortable('cancel');
							et_reinitialize_builder_layout();
						}

						// Remove insert row button if a row is pasted into specialty's column
						if ($this.is('.et-pb-column-specialty') && $this.find('.et_pb_row').length <= 1 && $this.find('.et-pb-insert-row').length) {
							$this.find('.et-pb-insert-row').remove();
						}
					},
					update : function( event, ui ) {
						// Loading process occurs. Dragging is temporarily disabled
						if ( ET_PageBuilder_App.isLoading ) {
							this_el.$el.sortable( 'cancel' );
							return;
						}

						// Split Testing adjustment :: module as subject / goal
						if ( ET_PageBuilder_AB_Testing.is_active() ) {
							var is_row_inner = $( ui.item ).hasClass( 'et_pb_row' ),
								cid = is_row_inner ? $( ui.item ).children( '.et-pb-row-content' ).attr( 'data-cid' ) : $( ui.item ).attr( 'data-cid' );

							// Check for permission user first
							if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( cid ) ) {
								ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
								this_el.$el.sortable('cancel');
								et_reinitialize_builder_layout();
								return;
							} else {
								// User has proper permission. Verify whether the action is permissible or not
								// IMPORTANT: update event is fired twice, once when the module is moved from its origin and once when the
								// module is landed on its destination. This causes two different way in deciding $sender and $target
								var $item      = $( ui.item ),
									$sender    = _.isEmpty( $( ui.sender ) ) ? $( event.target )  : $( ui.sender ),
									$target    = _.isEmpty( $( ui.sender ) ) ? $( event.toElement ).parent() : $( event.target ),
									is_subject = $item.hasClass('et_pb_ab_subject'),
									is_goal    = $item.hasClass('et_pb_ab_goal'),
									is_sender_inside_subject = $sender.closest('.et_pb_ab_subject').length,
									is_target_inside_subject = $target.closest('.et_pb_ab_subject').length,
									is_target_inside_goal = $target.closest('.et_pb_ab_goal').length;

								// Goal inside subject cannot be moved outside subject
								if ( is_goal && ! is_subject && is_sender_inside_subject ) {
									ET_PageBuilder_AB_Testing.alert( 'cannot_move_module_goal_out_from_subject' );
									$( this_el.$el ).sortable('cancel');
									et_reinitialize_builder_layout();
									return;
								}

								// Goal outside subject cannot be moved inside subject
								if ( is_goal && ! is_subject && ! is_sender_inside_subject && is_target_inside_subject ) {
									ET_PageBuilder_AB_Testing.alert( 'cannot_move_goal_into_subject' );
									$( this_el.$el ).sortable('cancel');
									et_reinitialize_builder_layout();
									return;
								}

								// Subject cannot be moved into goal (assuming goal is row or section)
								if ( is_subject && ! is_goal && is_target_inside_goal ) {
									ET_PageBuilder_AB_Testing.alert( 'cannot_move_subject_into_goal' );
									$( this_el.$el ).sortable('cancel');
									et_reinitialize_builder_layout();
									return;
								}
							}
						}

						var model,
							$module_block,
							module_cid = ui.item.data( 'cid' );

						$module_block = $( ui.item );

						if ( typeof module_cid === 'undefined' && $(event.target).is('.et-pb-column-specialty') ) {
							$module_block = $( ui.item ).closest( '.et_pb_row' ).find( '.et-pb-row-content' );

							module_cid = $module_block.data( 'cid' );
						}

						// if the column doesn't have modules, add the dragged module before 'Insert Module' button or append to column
						if ( ! $(event.target).is('.et-pb-column-specialty') && $( ui.item ).closest( event.target ).length && $( event.target ).find( '.et_pb_module_block' ).length === 1 ) {
							// if .et-pb-insert-module button exists, then add the module before that button. Otherwise append to column
							if ( $( event.target ).find( '.et-pb-insert-module' ).length ) {
								$module_block.insertBefore( $( event.target ).find( '.et-pb-insert-module' ) );
							} else {
								$( event.target ).append( $module_block );
							}
						}

						model = this_el.collection.find( function( model ) {
							return model.get('cid') == module_cid;
						} );

						// Enable history saving and set meta for history
						ET_PageBuilder_App.allowHistorySaving( 'moved', 'module', model.get( 'admin_label' ) );

						if ( model.get( 'parent' ) === this_el.model.attributes.cid && $( ui.item ).closest( event.target ).length ) {
							// order of items have been changed within the same row

							ET_PageBuilder_Events.trigger( 'et-model-changed-position-within-column' );
						} else {
							model.set( 'parent', this_el.model.attributes.cid );
						}

						// Prepare collection sorting based on layout position
						var column_cid             = parseInt( $(this).attr( 'data-cid') ),
							sibling_module_index   = 0;

						// Loop module block based on DOM position to ensure its index order
						$(this).find('.et_pb_module_block').each(function(){
							sibling_module_index++;

							var sibling_module_cid = parseInt( $(this).data('cid') ),
								layout_index       = column_cid + sibling_module_index,
								sibling_model      = ET_PageBuilder_Modules.findWhere({ cid : sibling_module_cid });

							// Set layout_index
							sibling_model.set({ layout_index : layout_index });
						});

						// Sort collection based on layout_index
						ET_PageBuilder_Modules.comparator = 'layout_index';
						ET_PageBuilder_Modules.sort();

						if ( ( $( ui.item ).closest( '.et_pb_section.et_pb_global' ).length || $( ui.item ).closest( '.et_pb_row.et_pb_global' ).length || $( ui.sender ).closest( '.et_pb_row.et_pb_global' ).length || $( ui.sender ).closest( '.et_pb_section.et_pb_global' ).length ) && '' === et_pb_options.template_post_id ) {
							var module_cid = ui.item.data( 'cid' ),
								$from_global_row = $( ui.sender ).closest( '.et_pb_row.et_pb_global' ),
								$to_global_row = $( ui.item ).closest( '.et_pb_row.et_pb_global' ),
								global_module_cid,
								$moving_from,
								$moving_to;

							$moving_from = $from_global_row.length > 0 ? $from_global_row : $( ui.sender ).closest( '.et_pb_section.et_pb_global' );
							$moving_to = $to_global_row.length > 0 ? $to_global_row : $( ui.item ).closest( '.et_pb_section.et_pb_global' );


							if ( $moving_from === $moving_to ) {
								var global_module_cid = $from_global_row.length > 0 ? $( ui.sender ).closest( '.et-pb-row-content' ).data( 'cid' ) : $( ui.sender ).closest( '.et-pb-section-content' ).data( 'cid' );

								et_pb_update_global_template( global_module_cid );
								et_reinitialize_builder_layout();
							} else {
								var $global_element = $moving_from;

								// remove global parent attributes if moved not to global parent.
								if ( $moving_to.length === 0 && ( ( ! _.isUndefined( model.get( 'et_pb_global_parent' ) ) && '' !== model.get( 'et_pb_global_parent' ) ) || ! _.isUndefined( model.get( 'global_parent_cid' ) ) ) ) {
									model.unset( 'et_pb_global_parent' );
									model.unset( 'global_parent_cid' );
									// remove global attributes from all the child components
									ET_PageBuilder_Layout.removeGlobalAttributes( ET_PageBuilder_Layout.getView( model.get( 'cid' ) ) );
								}

								for ( var i = 1; i <= 2; i++ ) {
									global_module_cid = typeof $global_element.find( '.et-pb-section-content' ).data( 'cid' ) !== 'undefined' ? $global_element.find( '.et-pb-section-content' ).data( 'cid' ) : $global_element.find( '.et-pb-row-content' ).data( 'cid' );

									if ( typeof global_module_cid !== 'undefined' && '' !== global_module_cid ) {

										et_pb_update_global_template( global_module_cid );
										et_reinitialize_builder_layout();
									}

									$global_element = $moving_to;
								}
							}
						}
					},
					start : function( event, ui ) {
						et_pb_close_all_right_click_options();

						// copy module if Alt key pressed
						if ( event.altKey ) {
							var is_row_inner = $( ui.item ).hasClass( 'et_pb_row' );
							var cid = is_row_inner ? $( ui.item ).children( '.et-pb-row-content' ).attr( 'data-cid' ) : $( ui.item ).attr( 'data-cid' );
							var movedModule = ET_PageBuilder_Layout.getView( cid );
							var view_settings = {
								model      : movedModule.model,
								view       : movedModule.$el,
								view_event : event
							};
							var clone_module = new ET_PageBuilder.RightClickOptionsView( view_settings, true );

							clone_module.copy( event, true );

							clone_module.pasteAfter( event, undefined, undefined, undefined, true, true );

							// Enable history saving and set meta for history
							ET_PageBuilder_App.allowHistorySaving( 'cloned', 'module', movedModule.model.get( 'admin_label' ) );
						}
					}
				} );

				return this;
			},

			addModule : function( event ) {
				var $event_target = $(event.target),
					$add_module_button = $event_target.is( 'span' ) ? $event_target.parent('.et-pb-insert-module') : $event_target;

				event.preventDefault();
				event.stopPropagation();

				if ( this.isColumnLocked() )
					return;

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'add_module' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}
				}

				if ( ! $add_module_button.parent().is( event.delegateTarget ) ) {
					return;
				}

				et_pb_close_all_right_click_options();

				if ($(event.target).is('.et-pb-insert-row')) {
					this.addRow();
				} else {
					var view;

					view = new ET_PageBuilder.ModalView( {
						model : this.model,
						collection : this.collection,
						attributes : {
							'data-open_view' : 'all_modules'
						},
						view : this
					} );

					$('body').append( view.render().el );
				}
			},

			// Add New Row functionality for the specialty section column
			addRow : function( appendAfter ) {
				var module_id = ET_PageBuilder_Layout.generateNewId(),
					global_parent = typeof this.model.get( 'et_pb_global_parent' ) !== 'undefined' && '' !== this.model.get( 'et_pb_global_parent' ) ? this.model.get( 'et_pb_global_parent' ) : '',
					global_parent_cid = '' !== global_parent ? this.model.get( 'global_parent_cid' ) : '',
					new_row_view;

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( this.isColumnLocked() ) {
					return;
				}

				this.collection.add( [ {
					type : 'row',
					module_type : 'row',
					cid : module_id,
					parent : this.model.get( 'cid' ),
					view : this,
					appendAfter : appendAfter,
					et_pb_global_parent : global_parent,
					global_parent_cid : global_parent_cid,
					admin_label : et_pb_options.noun['row']
				} ] );

				new_row_view = ET_PageBuilder_Layout.getView( module_id );
				new_row_view.displayColumnsOptions();
			},

			removeColumn : function() {
				var modules;

				modules = ET_PageBuilder_Layout.getChildViews( this.model.get('cid') );

				_.each( modules, function( module ) {
					if ( module.model.get( 'type' ) === 'row' || module.model.get( 'type' ) === 'row_inner' ) {
						module.removeRow();
					} else {
						module.removeModule();
					}
				} );

				ET_PageBuilder_Layout.removeView( this.model.get('cid') );

				this.model.destroy();

				this.remove();
			},

			isColumnLocked : function() {
				if ( 'on' === this.model.get( 'et_pb_locked' ) || 'on' === this.model.get( 'et_pb_parent_locked' ) ) {
					return true;
				}

				return false;
			},

			isColumnParentLocked : function( cid ) {
				var parent_view = ET_PageBuilder_Layout.getView( cid );

				if ( ! _.isUndefined( parent_view ) && ( 'on' === parent_view.model.get('et_pb_locked' ) || 'on' === parent_view.model.get('et_pb_parent_locked' ) ) ) {
					return true;
				}

				return false;
			},

			showRightClickOptions : function( event ) {
				event.preventDefault();

				var et_right_click_options_view,
					view_settings = {
						model      : this.model,
						view       : this.$el,
						view_event : event
					};

				// Fullwidth and regular section uses different type for column ( section vs column )
				// Add marker so it can be identified
				view_settings.model.attributes.is_insert_module = true;

				et_right_click_options_view = new ET_PageBuilder.RightClickOptionsView( view_settings );

				return;
			},

			hideRightClickOptions : function( event ) {
				event.preventDefault();

				et_pb_close_all_right_click_options();
			}

		} );

		ET_PageBuilder.ColumnSettingsView = window.wp.Backbone.View.extend( {

			className : 'et_pb_modal_settings',

			template : _.template( $('#et-builder-column-settings-template').html() ),

			events : {
				'click .et-pb-column-layouts li' : 'addColumns',
				'click .et-pb-options-tabs-links li a' : 'switchTab'
			},

			initialize : function( attributes ) {
				this.listenTo( ET_PageBuilder_Events, 'et-add:columns', this.removeView );
				this.listenTo( ET_PageBuilder_Events, 'et-modal-view-removed', this.removeViewAndEmptySection );

				this.options = attributes;
			},

			render : function() {
				this.$el.html( this.template( this.model.toJSON() ) );

				if ( ET_PageBuilder_Layout.getView( this.model.get('cid') ).$el.closest( '.et_pb_global' ).length ) {
					this.$el.addClass( 'et_pb_no_global' );
				}

				if ( typeof this.model.get( 'et_pb_specialty' ) !== 'undefined' && 'on' === this.model.get( 'et_pb_specialty' ) || typeof this.model.get( 'change_structure' ) !== 'undefined' && 'true' === this.model.get( 'change_structure' ) ) {
					this.$el.addClass( 'et_pb_modal_no_tabs' );
				}

				return this;
			},

			addColumns : function( event ) {
				event.preventDefault();

				var that = this,
					$layout_el = $(event.target).is( 'li' ) ? $(event.target) : $(event.target).closest( 'li' ),
					layout = $layout_el.data('layout'),
					layout_specialty = 'section' === that.model.get( 'type' ) && 'on' === that.model.get( 'et_pb_specialty' )
						? $layout_el.data('specialty').split(',')
						: '',
					column_settings = {
						'layout' : layout,
						'layout_specialty' : layout_specialty,
						'is_structure_change' : typeof that.model.get( 'change_structure' ) !== 'undefined' && 'true' === that.model.get( 'change_structure' ),
						'specialty_columns' : $layout_el.data('specialty_columns')
					};

				ET_PageBuilder_Layout.changeColumnStructure( that, column_settings );
			},

			removeView : function() {
				var that = this;

				// remove it with some delay to make sure animation applied to modal before removal
				setTimeout( function() {
					that.remove();
				}, 300 );
			},

			switchTab : function( event ) {
				var $this_el = $( event.currentTarget ).parent();

				event.preventDefault();

				et_handle_templates_switching( $this_el, 'row', '' );
			},

			/**
			 * Remove modal view and empty specialty section, if the user hasn't selected a section layout
			 * and closed a modal window
			 */
			removeViewAndEmptySection : function() {
				if ( this.model.get( 'et_pb_specialty' ) === 'on' ) {
					this.options.view.model.destroy();

					ET_PageBuilder_Layout.removeView( this.options.view.model.get('cid') );

					this.options.view.remove();
				}

				this.remove();
			}

		} );

		ET_PageBuilder.SaveLayoutSettingsView = window.wp.Backbone.View.extend( {

			className : 'et_pb_modal_settings',

			template : _.template( $('#et-builder-load_layout-template').html() ),

			events : {
				'click .et_pb_layout_button_load' : 'loadLayout',
				'click .et_pb_layout_button_delete' : 'deleteLayout',
				'click .et-pb-options-tabs-links li a' : 'switchTab'
			},

			initialize : function( attributes ) {
				this.options = attributes;

				this.layoutIsLoading = false;

				this.listenTo( ET_PageBuilder_Events, 'et-modal-view-removed', this.remove );
			},

			render : function() {
				var $this_el = this.$el,
					post_type = $('#post_type').val();

				$this_el.html( this.template( { "display_switcher" : "on" } ) );

				et_load_saved_layouts( 'predefined', 'et-pb-all-modules-tab', $this_el, post_type );
				et_load_saved_layouts( 'not_predefined', 'et-pb-saved-modules-tab', $this_el, post_type );

				return this;
			},

			deleteLayout : function( event ) {
				event.preventDefault();

				var $layout = $( event.currentTarget ).closest( 'li' );

				if ( $layout.hasClass( 'et_pb_deleting_layout' ) )
					return;
				else
					$layout.addClass( 'et_pb_deleting_layout' );

				$.ajax( {
					type: "POST",
					url: et_pb_options.ajaxurl,
					data:
					{
						action : 'et_pb_delete_layout',
						et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
						et_layout_id : $layout.data( 'layout_id' )
					},
					beforeSend : function() {
						ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

						$layout.css( 'opacity', '0.5' );
					},
					complete : function() {
						ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );
					},
					success: function( data ){
						if ( $layout.closest( 'ul' ).find( '> li' ).length == 1 )
							$layout.closest( 'ul' ).prev( 'h3' ).hide();

						$layout.remove();
					}
				} );
			},

			loadLayout : function( event ) {
				event.preventDefault();

				if ( this.layoutIsLoading ) {
					return;
				} else {
					this.layoutIsLoading = true;

					this.$el.find( '.et-pb-main-settings' ).css( { 'opacity' : '0.5' } );
				}

				var $layout = $( event.currentTarget ).closest( 'li' ),
					replace_content = $layout.closest( '.et-pb-main-settings' ).find( '#et_pb_load_layout_replace' ).is( ':checked' ),
					content = et_pb_get_content( 'content' ),
					this_el = this;

				$.ajax( {
					type: "POST",
					url: et_pb_options.ajaxurl,
					data:
					{
						action : 'et_pb_load_layout',
						et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
						et_layout_id : $layout.data( 'layout_id' ),
						et_replace_content : ( replace_content ? 'on' : 'off' )
					},
					beforeSend : function() {
						ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );
					},
					complete : function() {
						ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

						ET_PageBuilder_Events.trigger( 'et-saved_layout:loaded' );
					},
					success: function( data ){
						content = replace_content ? data : data + content;

						ET_PageBuilder_App.removeAllSections();

						if ( content !== '' ) {
							ET_PageBuilder_App.allowHistorySaving( 'loaded', 'layout' );
						}

						ET_PageBuilder_App.createNewLayout( content, 'load_layout' );
					}
				} );
			},

			switchTab: function( event ) {
				var $this_el = $( event.currentTarget ).parent();
				event.preventDefault();

				et_handle_templates_switching( $this_el, 'layout', '' );
			}

		} );

		ET_PageBuilder.ModulesView = window.wp.Backbone.View.extend( {

			className : 'et_pb_modal_settings',

			template : _.template( $('#et-builder-modules-template').html() ),

			events : {
				'click .et-pb-all-modules li' : 'addModule',
				'click .et-pb-options-tabs-links li a' : 'switchTab'
			},

			initialize : function( attributes ) {
				this.options = attributes;

				this.listenTo( ET_PageBuilder_Events, 'et-modal-view-removed', this.remove );
			},

			render : function() {
				var template_type_holder = typeof ET_PageBuilder_Layout.getView( this.model.get('parent') ) !== 'undefined' ? ET_PageBuilder_Layout.getView( this.model.get('parent') ) : this;
				this.$el.html( this.template( ET_PageBuilder_Layout.toJSON() ) );

				if ( ET_PageBuilder_Layout.getView( this.model.get('cid') ).$el.closest( '.et_pb_global' ).length || typeof template_type_holder.model.get('et_pb_template_type') !== 'undefined' && 'module' === template_type_holder.model.get('et_pb_template_type') ) {
					this.$el.addClass( 'et_pb_no_global' );
				}

				return this;
			},

			addModule : function( event ) {
				var $this_el             = $( event.currentTarget ),
					label                = $this_el.find( '.et_module_title' ).text(),
					type                 = $this_el.attr( 'class' ).replace( ' et_pb_fullwidth_only_module', '' ),
					global_module_cid    = '',
					parent_view          = ET_PageBuilder_Layout.getView( this.model.get('parent') ),
					template_type_holder = typeof parent_view !== 'undefined' ? parent_view : this

				event.preventDefault();

				if ( typeof this.model.get( 'et_pb_global_parent' ) !== 'undefined' && typeof this.model.get( 'et_pb_global_parent' ) !== '' ) {
					global_module_cid = this.model.get( 'global_parent_cid' );
				}

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'added', 'module', label );

				this.collection.add( [ {
					type : 'module',
					cid : ET_PageBuilder_Layout.generateNewId(),
					module_type : type,
					admin_label : label,
					parent : this.attributes['data-parent_cid'],
					view : this.options.view,
					global_parent_cid : global_module_cid
				} ] );

				this.remove();

				if ( '' !== global_module_cid ) {
					et_pb_update_global_template( global_module_cid );
				}

				if ( typeof template_type_holder.model.get( 'et_pb_template_type' ) !== 'undefined' && 'module' === template_type_holder.model.get( 'et_pb_template_type' ) ) {
					et_add_template_meta( '_et_pb_module_type', type );
				}

				et_pb_open_current_tab();
			},

			switchTab : function( event ) {
				var $this_el = $( event.currentTarget ).parent(),
					module_width = typeof this.model.get( 'et_pb_fullwidth' ) && 'on' === this.model.get( 'et_pb_fullwidth' ) ? 'fullwidth' : 'regular';

				event.preventDefault();

				et_handle_templates_switching( $this_el, 'module', module_width );
			}

		} );

		ET_PageBuilder.ModuleSettingsView = window.wp.Backbone.View.extend( {

			className : 'et_pb_module_settings',

			initialize : function() {
				if ( ! $( ET_PageBuilder_Layout.generateTemplateName( this.attributes['data-module_type'] ) ).length ) {
					this.attributes['data-no_template'] = 'no_template';
					return;
				}

				this.template = _.template( $( ET_PageBuilder_Layout.generateTemplateName( this.attributes['data-module_type'] ) ).html() );
				this.listenTo( ET_PageBuilder_Events, 'et-modal-view-removed', this.removeModule );
				this.listenTo( ET_PageBuilder_Events, 'et-advanced-module:saved', this.renderMap );
			},

			events : {
			},

			render : function() {
				var thisClass = this,
					$this_el = this.$el,
					content = '',
					this_module_cid = this.model.attributes.cid,
					$content_textarea,
					$content_textarea_container,
					$content_textarea_option,
					advanced_mode = false,
					view,
					$color_picker,
					$upload_button,
					$video_image_button,
					$gallery_button,
					$icon_font_list,
					$et_affect_fields,
					$et_form_validation,
					$icon_font_options = [ "et_pb_font_icon", "et_pb_button_one_icon", "et_pb_button_two_icon", "et_pb_button_icon" ],
					$add_email_account_buttons,
					$preview_panel,
					$previewable_upload_button_add,
					$previewable_upload_button_edit,
					$previewable_upload_button_delete;

				// Update module's _builder_version to current builder's version
				thisClass.model.set( 'et_pb__builder_version', et_pb_options.product_version );

				// Replace encoded double quotes with normal quotes,
				// escaping is applied in modules templates
				_.each( this.model.attributes, function( value, key, list ) {
					if ( typeof value === 'string' && key !== 'et_pb_content_new' && -1 === $.inArray( key, $icon_font_options ) && ! /^\%\%\d+\%\%$/.test( $.trim( value ) ) ) {
						return list[ key ] = value.replace( /%22/g, '"' );
					}
				} );

				this.$el.html( this.template( this.model.attributes ) );

				$content_textarea = this.$el.find( '#et_pb_content_new' );

				$color_picker = this.$el.find('.et-pb-color-picker-hex');

				$color_picker_alpha = this.$el.find('.et-builder-color-picker-alpha');

				$upload_button = this.$el.find('.et-pb-upload-button');

				$preview_panel = this.$el.find('.et-pb-option-container--upload .et-pb-option-preview');

				$previewable_upload_button_add = this.$el.find('.et-pb-option-container--upload .et-pb-option-preview-button--add');

				$previewable_upload_button_edit = this.$el.find('.et-pb-option-container--upload .et-pb-option-preview-button--edit');

				$previewable_upload_button_delete = this.$el.find('.et-pb-option-container--upload .et-pb-option-preview-button--delete');

				$video_image_button = this.$el.find('.et-pb-video-image-button');

				$gallery_button = this.$el.find('.et-pb-gallery-button');

				$time_picker = this.$el.find('.et-pb-date-time-picker');

				$icon_font_list = this.$el.find('.et_font_icon');

				$validation_element = $this_el.find('.et-validate-number');

				$et_form_validation = $this_el.find('form.validate');

				$warning = $this_el.find('.et-pb-option--warning');

				$add_email_account_buttons = $this_el.find('.et_pb_email_add_account');

				// validation
				if ( $et_form_validation.length ) {
					et_builder_debug_message('validation enabled');
					$et_form_validation.validate({
						debug: true
					});
				}

				if ( $color_picker.length ) {
					var draggingID,
						changeID;

					$color_picker.each( function() {
						var $this = $(this);

						$this.wpColorPicker({
							defaultColor : $this.data('default-color'),
							palettes     : '' !== et_pb_options.page_color_palette ? et_pb_options.page_color_palette.split( '|' ) : et_pb_options.default_color_palette.split( '|' ),
							change       : function( event, ui ) {
								var $this_el      = $(this),
									$option_container = $this_el.closest( '.et-pb-option-container' ),
									$reset_button = $option_container.find( '.et-pb-reset-setting' ),
									$custom_color_container = $this_el.closest( '.et-pb-custom-color-container' ),
									$preview = $option_container.find( '.et-pb-option-preview' ),
									has_preview = $this_el.hasClass('et-pb-color-picker-hex-has-preview'),
									is_gradient_colorpicker = $this_el.closest('.et_pb_background-tab--gradient').length > 0,
									current_value = $this_el.val(),
									default_value;

								if ( $custom_color_container.length ) {
									$custom_color_container.find( '.et-pb-custom-color-picker' ).val( ui.color.toString() );
								}

								default_value = et_pb_get_default_setting_value( $this_el );

								if ( current_value !== default_value ) {
									if ( $reset_button.length  ) {
										$reset_button.addClass( 'et-pb-reset-icon-visible' );
									}

									if ( has_preview ) {
										$preview.removeClass('et-pb-option-preview--empty');
									}
								} else {
									if ( $reset_button.length  ) {
										$reset_button.removeClass( 'et-pb-reset-icon-visible' );
									}

									if ( has_preview ) {
										$preview.addClass('et-pb-option-preview--empty');
									}
								}

								if ( has_preview) {
									$preview.css( {
										backgroundColor: current_value
									} );
								}

								if ( is_gradient_colorpicker ) {
									et_pb_update_gradient_preview( $this_el );
								}

								if ( _.has( event, 'originalEvent' ) && _.has( event.originalEvent, 'type' ) && event.originalEvent.type === 'square' ) {
									$option_container.find( '.button-confirm' ).css( 'backgroundColor', current_value + ' !important' );

									if ( ! $option_container.hasClass( 'is-dragging' ) ) {
										$option_container.addClass( 'is-dragging' );
									}

									clearTimeout( draggingID );

									draggingID = setTimeout( function() {
										et_pb_reposition_colorpicker_element( $this_el );

										$option_container.find( '.button-confirm' ).css( 'backgroundColor', '' );

										$option_container.removeClass( 'is-dragging' );
									}, 300 );
								}

								// Set focus back to clearpicker input after color change so enter could close the colorpicker
								clearTimeout( changeID );

								changeID = setTimeout( function() {
									$this_el.focus();
								}, 300 );

								// Remove clear marker class name to make color validation works again
								if ( $this_el.hasClass( 'et-pb-is-cleared' ) ) {
									$this_el.removeClass( 'et-pb-is-cleared' )
								}
							},
							clear: function() {
								$(this).val( et_pb_options.invalid_color );
								$(this).closest( '.et-pb-option-container' ).find( '.et-pb-main-setting' ).val( '' );
							},
							width        : $this.closest( '.et-pb-option--background' ).length ? 660 : 300,
							height       : 190,
							diviColorpicker : $this.closest( '.et-pb-option--background' ).length ? true : false
						});

						var default_color = $this.data('default-color') || '',
							$reset_button = $this.closest( '.et-pb-option-container' ).find( '.et-pb-reset-setting' );

						if ( $this.hasClass('et-pb-color-picker-hex-has-preview') ) {
							var $option_container = $this.closest('.et-pb-option-container');

							$option_container.find('.et-pb-option-preview-button--add, .et-pb-option-preview-button--edit, .et-pb-option-preview').click( function(e) {
								e.stopPropagation();

								$this.wpColorPicker('open');
							});

							$option_container.find('.et-pb-option-preview-button--delete').click( function(e) {
								e.stopPropagation();

								// Clear color value on DOM and colorpicker then add cleared class name to bypass color validation
								$this.wpColorPicker( 'color', '' ).val( '' ).addClass( 'et-pb-is-cleared' );

								$option_container.find('.et-pb-option-preview').removeAttr('style').addClass('et-pb-option-preview--empty');
							});
						}

						if ( ! $reset_button.length ) {
							return true;
						}

						if ( default_color !== $this.val() ) {
							$reset_button.addClass( 'et-pb-reset-icon-visible' );
						}
					} );
				}

				if ( $color_picker_alpha.length ) {
					$color_picker_alpha.each(function(){
						var $this_color_picker_alpha = $(this),
							color_picker_alpha_val = $this_color_picker_alpha.data('value').split('|'),
							color_picker_alpha_hex = color_picker_alpha_val[0] || '#444444',
							color_picker_alpha_opacity = color_picker_alpha_val[2] || 1.0;

						$this_color_picker_alpha.attr('data-opacity', color_picker_alpha_opacity );
						$this_color_picker_alpha.val( color_picker_alpha_hex );

						$this_color_picker_alpha.minicolors({
							control: 'hue',
							defaultValue: $(this).data('default-color') || '',
							opacity: true,
							changeDelay: 200,
							show: function() {
								$this_color_picker_alpha.minicolors('opacity', $this_color_picker_alpha.data('opacity') );
							},
							change: function(hex, opacity) {
								if( !hex ) {
									return;
								}

								var rgba_object = $this_color_picker_alpha.minicolors('rgbObject'),
									$field = $( $this_color_picker_alpha.data('field') ),
									values = [],
									values_string;

								values.push( hex );
								values.push( rgba_object.r + ', ' + rgba_object.g + ', ' + rgba_object.b );
								values.push( opacity );

								values_string = values.join('|');

								if ( $field.length ) {
									$field.val( values_string );
								}
							},
							theme: 'bootstrap'
						});
					});
				}

				if ( $upload_button.length ) {
					et_pb_activate_upload( $upload_button );
				}

				if ( $previewable_upload_button_add.length || $previewable_upload_button_edit.length || $preview_panel ) {
					function et_pb_trigger_upload_button( e ) {
						e.preventDefault();
						e.stopPropagation();

						if ( $(this).hasClass( 'et-pb-option-preview' ) && ! $(this).hasClass( 'et-pb-option-preview--empty' ) && $(this).siblings( '.button' ).attr( 'data-type' ) === 'video' ) {
							return;
						}

						$(this).closest( '.et-pb-option' ).find( '.et-pb-upload-button' ).trigger( 'click' );
					}

					$preview_panel.click( et_pb_trigger_upload_button );

					$previewable_upload_button_add.click( et_pb_trigger_upload_button );

					$previewable_upload_button_edit.click( et_pb_trigger_upload_button );
				}

				if ( $previewable_upload_button_delete.length ) {
					$previewable_upload_button_delete.click( function( e ) {
						e.preventDefault();
						e.stopPropagation();

						var $option  = $( this ).closest( '.et-pb-option' ),
							$input   = $option.find( '.et-pb-upload-field' ),
							$preview = $option.find( '.et-pb-option-preview' );

						$input.val( '' );

						$preview.addClass( 'et-pb-option-preview--empty' ).find('.et-pb-preview-content').remove();
					} );
				}

				if ( $video_image_button.length ) {
					et_pb_generate_video_image( $video_image_button );
				}

				if ( $gallery_button.length ) {
					et_pb_activate_gallery( $gallery_button );
				}

				if ( $add_email_account_buttons.length ) {
					et_pb_email_lists_buttons_setup( $add_email_account_buttons, this );
				}

				if ( $time_picker.length ) {
					$time_picker.datetimepicker();
				}

				if( $validation_element.length ){
					$validation_element.keyup( function() {
						var $this_el = $( this );

						if ( $this_el.val() < 0 || ( !$.isNumeric( $this_el.val() ) && $this_el.val() !== '' ) ) {
							$this_el.val( 0 );
						}

						if ( $this_el.val() > 100 ) {
							$this_el.val( 100 );
						}

						if ( $this_el.val() !=='' ) {
							$this_el.val( Math.round( $this_el.val() ) );
						}
					});
				}

				if ( $icon_font_list.length ) {
					var that = this;
					$icon_font_list.each( function() {
						var $this_icon_list     = $( this ),
							$icon_font_field    = $this_icon_list.siblings('.et-pb-font-icon'),
							current_symbol_val  = $.trim( $icon_font_field.val() ),
							$icon_font_symbols  = $this_icon_list.find( 'li' ),
							active_symbol_class = 'et_active',
							$current_symbol,
							top_offset,
							icon_index_number;

						function et_pb_icon_font_init() {
							if ( current_symbol_val !== '' ) {
								// font icon index is used now in the following format: %%index_number%%
								if ( current_symbol_val.search( /^%%/ ) !== -1 ) {
									icon_index_number = parseInt( current_symbol_val.replace( /%/g, '' ) );
									$current_symbol   = $this_icon_list.find( 'li' ).eq( icon_index_number );
								} else {
									// set the 1st icon as active if wrong value saved for current_symbol_val
									if ( '"' === current_symbol_val ) {
										$current_symbol = $this_icon_list.find( 'li' ).eq( 0 );
									} else {
										$current_symbol = $this_icon_list.find( 'li[data-icon="' + current_symbol_val + '"]' );
									}
								}

								$current_symbol.addClass( active_symbol_class );

								if ( $this_icon_list.is( ':visible' ) ) {
									setTimeout( function() {
										top_offset = $current_symbol.offset().top - $this_icon_list.offset().top;

										if ( top_offset > 0 ) {
											$this_icon_list.animate( { scrollTop : top_offset }, 0 );
										}
									}, 110 );
								}
							}
						}
						et_pb_icon_font_init();

						that.$el.find( '.et-pb-options-tabs-links' ).on( 'et_pb_main_tab:changed', et_pb_icon_font_init );

						$icon_font_symbols.click( function() {
							var $this_element = $(this),
								this_symbol   = $this_element.index();

							if ( $this_element.hasClass( active_symbol_class ) ) {
								return false;
							}

							$this_element.siblings( '.' + active_symbol_class ).removeClass( active_symbol_class ).end().addClass( active_symbol_class );

							this_symbol = '%%' + this_symbol + '%%';

							$icon_font_field.val( this_symbol );
						} );
					});
				}

				if ( $content_textarea.length ) {
					$content_textarea_option = $content_textarea.closest( '.et-pb-option' );

					if ( $content_textarea_option.hasClass( 'et-pb-option-advanced-module' ) )
						advanced_mode = true;

					if ( ! advanced_mode ) {
						$content_textarea_container = $content_textarea.closest( '.et-pb-option-container' );

						content = $content_textarea.html();

						$content_textarea.remove();

						$content_textarea_container.prepend( et_pb_content_html );

						setTimeout( function() {
							if ( typeof window.switchEditors !== 'undefined' ) {
								window.switchEditors.go( 'et_pb_content_new', et_get_editor_mode() );
							}

							et_pb_set_content( 'et_pb_content_new', content );

							window.wpActiveEditor = 'et_pb_content_new';
						}, 100 );
					} else {
						var view_cid = ET_PageBuilder_Layout.generateNewId();
						this.view_cid = view_cid;

						$content_textarea_option.hide();

						$content_textarea.attr( 'id', 'et_pb_content_main' );

						view = new ET_PageBuilder.AdvancedModuleSettingsView( {
							model : this,
							el : this.$el.find( '.et-pb-option-advanced-module-settings' ),
							attributes : {
								cid : view_cid
							}
						} );

						ET_PageBuilder_Layout.addView( view_cid, view );

						$content_textarea_option.before( view.render() );

						if ( $content_textarea.html() !== '' ) {
							view.generateAdvancedSortableItems( $content_textarea.html(), this.$el.find( '.et-pb-option-advanced-module-settings' ).data( 'module_type' ) );
							ET_PageBuilder_Events.trigger( 'et-advanced-module:updated_order', this.$el );
						}
					}
				}

				if ( $warning.length ) {
					$warning.each(function() {
						var $warning_option = $(this);
						var $warning_field = $warning_option.find('.et-pb-option-warning');
						var display_if = $warning_field.attr('data-display_if');
						var name = $warning_field.attr('data-name');

						if ( et_pb_options[name] === display_if ) {
							$warning_option.addClass('et-pb-option--warning-active');
						}
					});
				}

				this.renderMap();

				et_pb_init_main_settings( this.$el, this_module_cid );

				if ( ! advanced_mode ) {
					setTimeout( function() {
						$this_el.find('select, input, textarea, radio').filter(':eq(0)').focus();
					}, 1 );
				}

				return this;
			},

			removeModule : function() {
				// remove Module settings, when modal window is closed or saved

				this.remove();
			},

			is_latlng : function( address ) {
				var latlng = address.split( ',' ),
					lat = ! _.isUndefined( latlng[0] ) ? parseFloat( latlng[0] ) : false,
					lng = ! _.isUndefined( latlng[1] ) ? parseFloat( latlng[1] ) : false;

				if ( typeof google !== 'undefined' && lat && ! _.isNaN( lat ) && lng && ! _.isNaN( lng ) ) {
					return new google.maps.LatLng( lat, lng );
				}

				return false;
			},

			renderMap: function() {
				this_el = this,
				$map = this.$el.find('.et-pb-map');

				if ( typeof google !== 'undefined' && $map.length ) {
					view_cid = this.view_cid;

					var $address = this.$el.find('.et_pb_address'),
						$address_lat = this.$el.find('.et_pb_address_lat'),
						$address_lng = this.$el.find('.et_pb_address_lng'),
						$find_address = this.$el.find('.et_pb_find_address'),
						$zoom_level = this.$el.find('.et_pb_zoom_level'),
						geocoder = new google.maps.Geocoder(),
						markers = {};
					var geocode_address = function() {
						var address = $address.val();
						if ( address.length <= 0 ) {
							return;
						}
						geocoder.geocode( { 'address': address}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
								var result            = results[0],
									location          = result.geometry.location,
									address_is_latlng = this_el.is_latlng( address );

								// If user passes valid lat lng instead of address, override geocode with given lat & lng
								if ( address_is_latlng ) {
									location = address_is_latlng;
								}

								if ( ! isNaN( location.lat() ) && ! isNaN( location.lng() ) ) {
									$address.val( result.formatted_address);
									$address_lat.val(location.lat());
									$address_lng.val(location.lng());
									update_center( location );
								} else {
									alert( et_pb_options.map_pin_address_invalid );
								}
							} else {
								alert( et_pb_options.geocode_error + ': ' + status);
							}
						});
					}

					var update_center = function( LatLng ) {
						$map.map.setCenter( LatLng );
					}

					var update_zoom = function () {
						$map.map.setZoom( parseInt( $zoom_level.val() ) );
					}

					$address.on('blur', geocode_address );
					$find_address.on('click', function(e){
						e.preventDefault();
					});

					$zoom_level.on('blur', update_zoom );

					setTimeout( function() {
						$map.map = new google.maps.Map( $map[0], {
							zoom: parseInt( $zoom_level.val() ),
							mapTypeId: google.maps.MapTypeId.ROADMAP
						});

						if ( '' != $address_lat.val() && '' != $address_lng.val() ) {
							update_center( new google.maps.LatLng( $address_lat.val(), $address_lng.val() ) );
						}

						if ( '' != $zoom_level ) {
							update_zoom();
						}

						setTimeout( function() {
							var map_pins = ET_PageBuilder_Layout.getChildViews( view_cid );
							if ( _.size( map_pins ) ) {
								_.each( map_pins, function( map_pin, key ) {

									// Skip current map pin if it has no lat or lng, as it will trigger maximum call stack exceeded
									if ( _.isUndefined( map_pin.model.get('et_pb_pin_address_lat') ) || _.isUndefined( map_pin.model.get('et_pb_pin_address_lng') ) ) {
										return;
									}

									markers[key] = new google.maps.Marker({
										map: $map.map,
										position: new google.maps.LatLng( parseFloat( map_pin.model.get('et_pb_pin_address_lat') ) , parseFloat( map_pin.model.get('et_pb_pin_address_lng') ) ),
										title: map_pin.model.get('et_pb_title'),
										icon: { url: et_pb_options.images_uri + '/marker.png', size: new google.maps.Size( 46, 43 ), anchor: new google.maps.Point( 16, 43 ) },
										shape: { coord: [1, 1, 46, 43], type: 'rect' }
									});
								});
							}
						}, 500 );

						google.maps.event.addListener( $map.map, 'center_changed', function() {
							var center = $map.map.getCenter();
							$address_lat.val( center.lat() );
							$address_lng.val( center.lng() );
						});

						google.maps.event.addListener( $map.map, 'zoom_changed', function() {
							var zoom_level = $map.map.getZoom();
							$zoom_level.val( zoom_level );
						});

					}, 200 );
				}
			}

		} );

		ET_PageBuilder.AdvancedModuleSettingsView = window.wp.Backbone.View.extend( {
			initialize : function() {
				this.listenTo( ET_PageBuilder_Events, 'et-advanced-module:updated', this.generateContent );

				this.listenTo( ET_PageBuilder_Events, 'et-modal-view-removed', this.removeModule );

				this.module_type = this.$el.data( 'module_type' );

				ET_PageBuilder.Events = ET_PageBuilder_Events;

				this.child_views = [];

				this.$el.attr( 'data-cid', this.attributes['cid'] );

				this.$sortable_options = this.$el.find('.et-pb-sortable-options');

				this.$content_textarea = this.$el.siblings('.et-pb-option-main-content').find('#et_pb_content_main');

				this.$sortable_options.sortable( {
					axis : 'y',
					cancel : '.et-pb-advanced-setting-remove, .et-pb-advanced-setting-options',
					update : function( event, ui ) {
						ET_PageBuilder_Events.trigger( 'et-advanced-module:updated' );
						ET_PageBuilder_Events.trigger( 'et-advanced-module:updated_order' );
					}
				} );

				this.$add_sortable_item = this.$el.find( '.et-pb-add-sortable-option' ).addClass( 'et-pb-add-sortable-initial' );
			},

			events : {
				'click .et-pb-add-sortable-option' : 'addModule',
				'click .et-pb-advanced-setting-clone' : 'cloneModule'
			},

			render : function() {
				return this;
			},

			addModule : function( event ) {
				event.preventDefault();

				this.model.collection.add( [ {
					type : 'module',
					module_type : this.module_type,
					cid : ET_PageBuilder_Layout.generateNewId(),
					view : this,
					created : 'manually',
					mode : 'advanced',
					parent : this.attributes['cid'],
					parent_cid : this.model.model.attributes['cid']
				} ], { update_shortcodes : 'false' } );

				this.$add_sortable_item.removeClass( 'et-pb-add-sortable-initial' );
				ET_PageBuilder_Events.trigger( 'et-advanced-module:updated_order' );
			},

			cloneModule : function( event ) {

				event.preventDefault();
				var cloned_cid = $( event.target ).closest( 'li' ).data( 'cid' ),
					cloned_model = ET_PageBuilder_App.collection.find( function( model ) {
						return model.get('cid') == cloned_cid;
					} ),
					module_attributes = _.clone( cloned_model.attributes );

				module_attributes.created = 'manually';
				module_attributes.cloned_cid = cloned_cid;
				module_attributes.cid = ET_PageBuilder_Layout.generateNewId();

				this.model.collection.add( module_attributes );

				ET_PageBuilder_Events.trigger( 'et-advanced-module:updated' );
				ET_PageBuilder_Events.trigger( 'et-advanced-module:saved' );
				ET_PageBuilder_Events.trigger( 'et-advanced-module:updated_order' );
			},

			generateContent : function() {
				var content = '';

				this.$sortable_options.find( 'li' ).each( function() {
					var $this_el = $(this);

					content += ET_PageBuilder_App.generateModuleShortcode( $this_el, false );
				} );

				// Replace double quotes with ^^ in temporary shortcodes
				content = content.replace( /%22/g, '^^' );

				this.$content_textarea.html( content );

				if ( ! this.$sortable_options.find( 'li' ).length )
					this.$add_sortable_item.addClass( 'et-pb-add-sortable-initial' );
				else
					this.$add_sortable_item.removeClass( 'et-pb-add-sortable-initial' );
			},

			generateAdvancedSortableItems : function( content, module_type ) {
				var this_el = this,
					et_pb_shortcodes_tags = ET_PageBuilder_App.getShortCodeChildTags(),
					reg_exp = window.wp.shortcode.regexp( et_pb_shortcodes_tags ),
					inner_reg_exp = ET_PageBuilder_App.wp_regexp_not_global( et_pb_shortcodes_tags ),
					matches = content.match( reg_exp );

				if ( content !== '' )
					this.$add_sortable_item.removeClass( 'et-pb-add-sortable-initial' );

				_.each( matches, function ( shortcode ) {
					var shortcode_element = shortcode.match( inner_reg_exp ),
						shortcode_name = shortcode_element[2],
						shortcode_attributes = shortcode_element[3] !== ''
							? window.wp.shortcode.attrs( shortcode_element[3] )
							: '',
						shortcode_content = shortcode_element[5],
						module_cid = ET_PageBuilder_Layout.generateNewId(),
						module_settings,
						prefixed_attributes = {},
						found_inner_shortcodes = typeof shortcode_content !== 'undefined' && shortcode_content !== '' && shortcode_content.match( reg_exp );

					module_settings = {
						type : 'module',
						module_type : module_type,
						cid : ET_PageBuilder_Layout.generateNewId(),
						view : this_el,
						created : 'auto',
						mode : 'advanced',
						parent : this_el.attributes['cid'],
						parent_cid : this_el.model.model.attributes['cid']
					};

					if ( _.isObject( shortcode_attributes['named'] ) ) {
						for ( var key in shortcode_attributes['named'] ) {
							var prefixed_key = key !== 'admin_label' ? 'et_pb_' + key : key,
								setting_value;

							if ( shortcode_name === 'column' && prefixed_key === 'et_pb_type' )
								prefixed_key = 'layout';

							setting_value = shortcode_attributes['named'][key];

							// Replace temporary ^^ signs with double quotes
							setting_value = setting_value.replace( /\^\^/g, '"' );

							prefixed_attributes[prefixed_key] = setting_value;
						}

						module_settings['et_pb_content_new'] = shortcode_content;

						module_settings = _.extend( module_settings, prefixed_attributes );
					}

					if ( ! found_inner_shortcodes ) {
						module_settings['et_pb_content_new'] = shortcode_content;
					}

					this_el.model.collection.add( [ module_settings ], { update_shortcodes : 'false' } );
				} );
			},

			removeModule : function() {
				// remove Module settings, when modal window is closed or saved

				_.each( this.child_views, function( view ) {
					view.removeView();
				} );

				this.remove();
			}

		} );

		ET_PageBuilder.AdvancedModuleSettingView = window.wp.Backbone.View.extend( {
			tagName : 'li',

			initialize : function() {
				this.template = _.template( $( '#et-builder-advanced-setting' ).html() );
			},

			events : {
				'click .et-pb-advanced-setting-options' : 'showSettings',
				'click .et-pb-advanced-setting-remove' : 'removeView'
			},

			render : function() {
				var view;

				this.$el.html( this.template( this.model.attributes ) );

				view = new ET_PageBuilder.AdvancedModuleSettingTitleView( {
					model : this.model,
					view : this
				} );

				this.$el.prepend( view.render().el );

				this.child_view = view;

				if ( typeof this.model.get( 'cloned_cid' ) === 'undefined' || '' === this.model.get( 'cloned_cid' ) ) {
					this.showSettings();
				}

				return this;
			},

			showSettings : function( event ) {
				var view;

				if ( event ) event.preventDefault();

				view = new ET_PageBuilder.AdvancedModuleSettingEditViewContainer( {
					view : this,
					attributes : {
						show_settings_clicked : ( event ? true : false )
					}
				} );

				$('.et_pb_modal_settings_container').after( view.render().el );
			},

			removeView : function( event ) {
				if ( event ) event.preventDefault();

				this.child_view.remove();

				this.remove();

				this.model.destroy();

				ET_PageBuilder_Events.trigger( 'et-advanced-module:updated' );
				ET_PageBuilder_Events.trigger( 'et-advanced-module:updated_order' );
			}
		} );

		ET_PageBuilder.AdvancedModuleSettingTitleView = window.wp.Backbone.View.extend( {
			tagName : 'span',

			className : 'et-sortable-title',

			initialize : function() {
				template_name = '#et-builder-advanced-setting-' + this.model.get( 'module_type' ) + '-title';

				this.template = _.template( $( template_name ).html() );

				this.listenTo( ET_PageBuilder_Events, 'et-advanced-module:updated', this.render );
			},

			render : function() {
				var view;

				// If admin label is empty, delete it so builder will use heading value instead
				if ( ! _.isUndefined( this.model.attributes.et_pb_admin_title ) && this.model.attributes.et_pb_admin_title === '' ) {
					delete this.model.attributes.et_pb_admin_title;
				}

				this.$el.html( this.template( this.model.attributes ) );

				return this;
			}
		} );

		ET_PageBuilder.AdvancedModuleSettingEditViewContainer = window.wp.Backbone.View.extend( {
			className : 'et_pb_modal_settings_container',

			initialize : function() {
				this.template = _.template( $( '#et-builder-advanced-setting-edit' ).html() );

				this.model = this.options.view.model;

				this.listenTo( ET_PageBuilder_Events, 'et-modal-view-removed', this.removeView );
			},

			events : {
				'click .et-pb-modal-save' : 'saveSettings',
				'click .et-pb-modal-close' : 'removeView'
			},

			is_latlng : function( address ) {
				var latlng = address.split( ',' ),
					lat = ! _.isUndefined( latlng[0] ) ? parseFloat( latlng[0] ) : false,
					lng = ! _.isUndefined( latlng[1] ) ? parseFloat( latlng[1] ) : false;

				if ( lat && ! _.isNaN( lat ) && lng && ! _.isNaN( lng ) ) {
					return new google.maps.LatLng( lat, lng );
				}

				return false;
			},

			render : function() {
				var this_module_cid = this.model.attributes.cid,
					view,
					$color_picker,
					$upload_button,
					$video_image_button,
					$map,
					$social_network_picker,
					$icon_font_list,
					this_el = this,
					$preview_panel,
					$previewable_upload_button_add,
					$previewable_upload_button_edit,
					$previewable_upload_button_delete;

				this.$el.html( this.template() );

				this.$el.addClass( 'et_pb_modal_settings_container_step2' );
				this.$el.attr( 'data-parent-cid', this_module_cid );

				if ( this.model.get( 'created' ) !== 'auto' || this.attributes['show_settings_clicked'] ) {
					view = new ET_PageBuilder.AdvancedModuleSettingEditView( { view : this } );

					this.$el.append( view.render().el );

					this.child_view = view;
				}

				ET_PageBuilder.Events.trigger( 'et-advanced-module-settings:render', this );

				$color_picker = this.$el.find('.et-pb-color-picker-hex');

				$color_picker_alpha = this.$el.find('.et-builder-color-picker-alpha');

				if ( $color_picker.length ) {
					var draggingID,
						changeID;

					$color_picker.each( function() {
						var $this = $(this);

						$this.wpColorPicker({
							defaultColor : $this.data('default-color'),
							palettes     : '' !== et_pb_options.page_color_palette ? et_pb_options.page_color_palette.split( '|' ) : et_pb_options.default_color_palette.split( '|' ),
							change       : function( event, ui ) {
								var $this_el      = $(this),
									$option_container = $this_el.closest( '.et-pb-option-container' ),
									$reset_button = $option_container.find( '.et-pb-reset-setting' ),
									$custom_color_container = $this_el.closest( '.et-pb-custom-color-container' ),
									$preview = $option_container.find( '.et-pb-option-preview' ),
									has_preview = $this_el.hasClass('et-pb-color-picker-hex-has-preview'),
									is_gradient_colorpicker = $this_el.closest('.et_pb_background-tab--gradient').length > 0,
									current_value = $this_el.val(),
									default_value;

								if ( $custom_color_container.length ) {
									$custom_color_container.find( '.et-pb-custom-color-picker' ).val( ui.color.toString() );
								}

								if ( ! $reset_button.length && ( ! has_preview && ! is_gradient_colorpicker ) ) {
									return;
								}

								default_value = et_pb_get_default_setting_value( $this_el );

								if ( current_value !== default_value ) {
									$reset_button.addClass( 'et-pb-reset-icon-visible' );

									if ( has_preview ) {
										$preview.removeClass('et-pb-option-preview--empty');
									}
								} else {
									$reset_button.removeClass( 'et-pb-reset-icon-visible' );

									if ( has_preview ) {
										$preview.addClass('et-pb-option-preview--empty');
									}
								}

								if ( has_preview) {
									$preview.css( {
										backgroundColor: current_value
									} );
								}

								if ( is_gradient_colorpicker ) {
									et_pb_update_gradient_preview( $this_el );
								}

								if ( has_preview || is_gradient_colorpicker ) {
									if ( _.has( event, 'originalEvent' ) && _.has( event.originalEvent, 'type' ) && event.originalEvent.type === 'square' ) {
										$option_container.find( '.button-confirm' ).css( 'backgroundColor', current_value + ' !important' );

										if ( ! $option_container.hasClass( 'is-dragging' ) ) {
											$option_container.addClass( 'is-dragging' );
										}

										clearTimeout( draggingID );

										draggingID = setTimeout( function() {
											et_pb_reposition_colorpicker_element( $this_el );

											$option_container.find( '.button-confirm' ).css( 'backgroundColor', '' );

											$option_container.removeClass( 'is-dragging' );
										}, 300 );
									}

									// Set focus back to clearpicker input after color change so enter could close the colorpicker
									clearTimeout( changeID );

									changeID = setTimeout( function() {
										$this_el.focus();
									}, 300 );
								}

								// Remove clear marker class name to make color validation works again
								if ( $this_el.hasClass( 'et-pb-is-cleared' ) ) {
									$this_el.removeClass( 'et-pb-is-cleared' )
								}
							},
							width        : $this.closest( '.et-pb-option--background' ).length ? 660 : 300,
							height       : 190,
							diviColorpicker : $this.closest( '.et-pb-option--background' ).length ? true : false
						});

						if ( $this.hasClass('et-pb-color-picker-hex-has-preview') ) {
							var $option_container = $this.closest('.et-pb-option-container');

							$option_container.find('.et-pb-option-preview-button--add, .et-pb-option-preview-button--edit, .et-pb-option-preview').click( function(e) {
								e.stopPropagation();

								$this.wpColorPicker('open');
							});

							$option_container.find('.et-pb-option-preview-button--delete').click( function(e) {
								e.stopPropagation();

								// Remove clear marker class name to make color validation works again
								$this.wpColorPicker( 'color', '' ).val( '' ).addClass( 'et-pb-is-cleared' );

								$option_container.find('.et-pb-option-preview').removeAttr('style').addClass('et-pb-option-preview--empty');
							});
						}
					} );
				}

				if ( $color_picker_alpha.length ) {
					$color_picker_alpha.each(function(){
						var $this_color_picker_alpha = $(this),
							color_picker_alpha_val = $this_color_picker_alpha.data('value').split('|'),
							color_picker_alpha_hex = color_picker_alpha_val[0] || '#444444',
							color_picker_alpha_opacity = color_picker_alpha_val[2] || 1.0;

						$this_color_picker_alpha.attr('data-opacity', color_picker_alpha_opacity );
						$this_color_picker_alpha.val( color_picker_alpha_hex );

						$this_color_picker_alpha.minicolors({
							control: 'hue',
							defaultValue: $(this).data('default-color') || '',
							opacity: true,
							changeDelay: 200,
							show: function() {
								$this_color_picker_alpha.minicolors('opacity', $this_color_picker_alpha.data('opacity') );
							},
							change: function(hex, opacity) {
								if( !hex ) {
									return;
								}

								var rgba_object = $this_color_picker_alpha.minicolors('rgbObject'),
									$field = $( $this_color_picker_alpha.data('field') ),
									values = [],
									values_string;

								values.push( hex );
								values.push( rgba_object.r + ', ' + rgba_object.g + ', ' + rgba_object.b );
								values.push( opacity );

								values_string = values.join('|');

								if ( $field.length ) {
									$field.val( values_string );
								}
							},
							theme: 'bootstrap'
						});
					});
				}

				$upload_button = this.$el.find('.et-pb-upload-button');

				$preview_panel = this.$el.find('.et-pb-option-container--upload .et-pb-option-preview');

				$previewable_upload_button_add = this.$el.find('.et-pb-option-container--upload .et-pb-option-preview-button--add');

				$previewable_upload_button_edit = this.$el.find('.et-pb-option-container--upload .et-pb-option-preview-button--edit');

				$previewable_upload_button_delete = this.$el.find('.et-pb-option-container--upload .et-pb-option-preview-button--delete');

				if ( $upload_button.length ) {
					et_pb_activate_upload( $upload_button );
				}

				if ( $previewable_upload_button_add.length || $previewable_upload_button_edit.length || $preview_panel.length ) {
					function et_pb_trigger_upload_button( e ) {
						e.preventDefault();
						e.stopPropagation();

						if ( $(this).hasClass( 'et-pb-option-preview' ) && ! $(this).hasClass( 'et-pb-option-preview--empty' ) ) {
							return;
						}

						$(this).closest( '.et-pb-option' ).find( '.et-pb-upload-button' ).trigger( 'click' );
					}

					$preview_panel.click( et_pb_trigger_upload_button );

					$previewable_upload_button_add.click( et_pb_trigger_upload_button );

					$previewable_upload_button_edit.click( et_pb_trigger_upload_button );
				}

				if ( $previewable_upload_button_delete.length ) {
					$previewable_upload_button_delete.click( function( e ) {
						e.preventDefault();
						e.stopPropagation();

						var $option  = $( this ).closest( '.et-pb-option' ),
							$input   = $option.find( '.et-pb-upload-field' ),
							$preview = $option.find( '.et-pb-option-preview' );

						$input.val( '' );

						$preview.addClass( 'et-pb-option-preview--empty' ).find('.et-pb-preview-content').remove();
					} );
				}

				$video_image_button = this.$el.find('.et-pb-video-image-button');

				if ( $video_image_button.length ) {
					et_pb_generate_video_image( $video_image_button );
				}

				$map = this.$el.find('.et-pb-map');

				if ( typeof google !== 'undefined' && $map.length ) {
					var map,
						marker,
						$address = this.$el.find('.et_pb_pin_address'),
						$address_lat = this.$el.find('.et_pb_pin_address_lat'),
						$address_lng = this.$el.find('.et_pb_pin_address_lng'),
						$find_address = this.$el.find('.et_pb_find_address'),
						$zoom_level = this.$el.find('.et_pb_zoom_level'),
						geocoder = new google.maps.Geocoder();
					var geocode_address = function() {
						var address = $address.val().trim();
						if ( address.length <= 0 ) {
							return;
						}
						geocoder.geocode( { 'address': address}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
								var result            = results[0],
									location          = result.geometry.location,
									address_is_latlng = this_el.is_latlng( address );

								// If user passes valid lat lng instead of address, override geocode with given lat & lng
								if ( address_is_latlng ) {
									location = address_is_latlng;
								}

								if ( ! isNaN( location.lat() ) && ! isNaN( location.lng() ) ) {
									$address.val( result.formatted_address);
									$address_lat.val(location.lat());
									$address_lng.val(location.lng());
									update_map( location );
								} else {
									alert( et_pb_options.map_pin_address_invalid );
								}
							} else {
								alert( et_pb_options.geocode_error + ': ' + status);
							}
						});
					}

					var update_map = function( LatLng ) {
						marker.setPosition( LatLng );
						map.setCenter( LatLng );
					}

					$address.on('change', geocode_address );
					$find_address.on('click', function(e){
						e.preventDefault();
					});

					setTimeout( function() {
						map = new google.maps.Map( $map[0], {
							zoom: parseInt( $zoom_level.val() ),
							mapTypeId: google.maps.MapTypeId.ROADMAP
						});

						marker = new google.maps.Marker({
							map: map,
							draggable: true,
							icon: { url: et_pb_options.images_uri + '/marker.png', size: new google.maps.Size( 46, 43 ), anchor: new google.maps.Point( 16, 43 ) },
							shape: { coord: [1, 1, 46, 43], type: 'rect' },
						});

						google.maps.event.addListener(marker, 'dragend', function() {
							var drag_position = marker.getPosition();
							$address_lat.val(drag_position.lat());
							$address_lng.val(drag_position.lng());

							update_map(drag_position);

							latlng = new google.maps.LatLng( drag_position.lat(), drag_position.lng() );
							geocoder.geocode({'latLng': latlng }, function(results, status) {
								if (status == google.maps.GeocoderStatus.OK) {
									if ( results[0] ) {
										$address.val( results[0].formatted_address );
									} else {
										alert( et_pb_options.no_results );
									}
								} else {
									alert( et_pb_options.geocode_error_2 + ': ' + status);
								}
							});

						});

						if ( '' != $address_lat.val() && '' != $address_lng.val() ) {
							update_map( new google.maps.LatLng( $address_lat.val(), $address_lng.val() ) );
						}
					}, 200 );
				}

				$gallery_button = this.$el.find('.et-pb-gallery-button');

				if ( $gallery_button.length ) {
					et_pb_activate_gallery( $gallery_button );
				}

				$social_network_picker = this.$el.find('.et-pb-social-network');

				if ( $social_network_picker.length ) {
					var $color_reset = this.$el.find('.reset-default-color'),
						$social_network_icon_color = this.$el.find('#et_pb_bg_color');
					if ( $color_reset.length ){
						$color_reset.click(function(){
							$main_settings = $color_reset.parents('.et-pb-main-settings');
							$social_network_picker = $main_settings.find('.et-pb-social-network');
							$social_network_icon_color = $main_settings.find('#et_pb_bg_color');
							if ( $social_network_icon_color.length ) {
								$social_network_icon_color.wpColorPicker('color', $social_network_picker.find( 'option:selected' ).data('color') );
								$color_reset.css( 'display', 'none' );
							}
						});
					}

					$social_network_picker.change(function(){
						$main_settings = $social_network_picker.parents('.et-pb-main-settings');

						if ( $social_network_picker.val().length ) {
							var $social_network_title = $main_settings.find('#et_pb_content_new'),
								$social_network_icon_color = $main_settings.find('#et_pb_bg_color');

							if ( $social_network_title.length ) {
								$social_network_title.val( $social_network_picker.find( 'option:selected' ).text() );
							}

							if ( $social_network_icon_color.length ) {
								$social_network_icon_color.wpColorPicker('color', $social_network_picker.find( 'option:selected' ).data('color') );
							}
						}
					});

					if ( $social_network_icon_color.val() !== $social_network_picker.find( 'option:selected' ).data('color') ) {
						$color_reset.css( 'display', 'inline' );
					}

				}

				$icon_font_list = this.$el.find('.et_font_icon');

				if ( $icon_font_list.length ) {
					var that = this;
					$icon_font_list.each( function() {
						var $this_icon_list = $( this ),
							$icon_font_field    = $this_icon_list.siblings('.et-pb-font-icon'),
							current_symbol_val  = $.trim( $icon_font_field.val() ),
							$icon_font_symbols  = $this_icon_list.find( 'li' ),
							active_symbol_class = 'et_active',
							$current_symbol,
							top_offset,
							icon_index_number;

						function et_pb_icon_font_init() {
							if ( current_symbol_val !== '' ) {
								// font icon index is used now in the following format: %%index_number%%
								if ( current_symbol_val.search( /^%%/ ) !== -1 ) {
									icon_index_number = parseInt( current_symbol_val.replace( /%/g, '' ) );
									$current_symbol   = $this_icon_list.find( 'li' ).eq( icon_index_number );
								} else {
									$current_symbol = $this_icon_list.find( 'li[data-icon="' + current_symbol_val + '"]' );
								}

								$current_symbol.addClass( active_symbol_class );

								if ( $this_icon_list.is( ':visible' ) ) {
									setTimeout( function() {
										top_offset = $current_symbol.offset().top - $this_icon_list.offset().top;

										if ( top_offset > 0 ) {
											$this_icon_list.animate( { scrollTop : top_offset }, 0 );
										}
									}, 110 );
								}
							}
						}
						et_pb_icon_font_init();

						that.$el.find( '.et-pb-options-tabs-links' ).on( 'et_pb_main_tab:changed', et_pb_icon_font_init );

						$icon_font_symbols.click( function() {
							var $this_element = $(this),
								this_symbol   = $this_element.index();

							if ( $this_element.hasClass( active_symbol_class ) ) {
								return false;
							}

							$this_element.siblings( '.' + active_symbol_class ).removeClass( active_symbol_class ).end().addClass( active_symbol_class );

							this_symbol = '%%' + this_symbol + '%%';

							$icon_font_field.val( this_symbol );
						} );
					});
				}

				et_pb_set_child_defaults( this.$el, this_module_cid );

				et_pb_init_main_settings( this.$el, this_module_cid );

				return this;
			},

			removeView : function( event ) {
				if ( event ) event.preventDefault();

				// remove advanced tab WYSIWYG, only if the close button is clicked
				if ( this.$el.find( '#et_pb_content_new' ) && event )
					et_pb_tinymce_remove_control( 'et_pb_content_new' );

				et_pb_hide_active_color_picker( this );

				if ( this.child_view )
					this.child_view.remove();

				this.remove();
			},

			saveSettings : function( event ) {
				var this_view = this,
					attributes = {},
					this_model_defaults = this.model.get( 'module_defaults' ) || '';

				event.preventDefault();

				this.$( 'input, select, textarea' ).each( function() {
					var $this_el = $(this),
						id = $this_el.attr('id'),
						setting_value;
						/*checked_values = [],
						name = $this_el.is('#et_pb_content_main') ? 'et_pb_content_new' : $this_el.attr('id');*/

					if ( typeof id === 'undefined' || ( -1 !== id.indexOf( 'qt_' ) && 'button' === $this_el.attr( 'type' ) ) ) {
						// settings should have an ID and shouldn't be a Quick Tag button from the tinyMCE in order to be saved
						return true;
					}

					id = $this_el.attr('id').replace( 'data.', '' );

					setting_value = $this_el.is('#et_pb_content_new')
						? et_pb_get_content( 'et_pb_content_new' )
						: $this_el.val();

					// do not save the default values into module attributes
					if ( '' !== this_model_defaults && typeof this_model_defaults[id] !== 'undefined' && this_model_defaults[id] === setting_value ) {
						this_view.model.unset( id );
						return true;
					}

					if ( $this_el.closest( '.et-pb-custom-css-option' ).length ) {
						// Custom CSS settings content should be modified before it is added to the shortcode attribute
						// replace new lines with || in Custom CSS settings
						setting_value = '' !== setting_value ? setting_value.replace( /(?:\r\n|\r|\n)/g, '\|\|' ) : '';
					}

					attributes[ id ] = setting_value;
				} );

				// Check if this is map module's pin view
				if ( ! _.isUndefined( attributes.et_pb_pin_address ) && ! _.isUndefined( attributes.et_pb_pin_address_lat ) && ! _.isUndefined( attributes.et_pb_pin_address_lng ) ) {
					// None of et_pb_pin_address, et_pb_pin_address_lat, and et_pb_pin_address_lng fields can be empty
					// If one of them is empty, it'll trigger Uncaught RangeError: Maximum call stack size exceeded message
					if ( attributes.et_pb_pin_address === '' || attributes.et_pb_pin_address_lat === '' || attributes.et_pb_pin_address_lng === '' ) {
						alert( et_pb_options.map_pin_address_error );
						return;
					}
				}

				this.model.set( attributes, { silent : true } );

				ET_PageBuilder_Events.trigger( 'et-advanced-module:updated' );
				ET_PageBuilder_Events.trigger( 'et-advanced-module:saved' );

				et_pb_tinymce_remove_control( 'et_pb_content_new' );

				this.removeView();
			}
		} );

		ET_PageBuilder.AdvancedModuleSettingEditView = window.wp.Backbone.View.extend( {
			className : 'et_pb_module_settings',

			initialize : function() {
				this.model = this.options.view.options.view.model;

				this.template = _.template( $( '#et-builder-advanced-setting-' + this.model.get( 'module_type' ) ).html() );
			},

			events : {
			},

			render : function() {
				var $this_el = this.$el,
					$content_textarea,
					$content_textarea_container;

				this.$el.html( this.template( { data : this.model.toJSON() } ) );

				this.$el.find( '.et-pb-main-settings' ).addClass( 'et-pb-main-settings-advanced' );

				$content_textarea = this.$el.find( 'div#et_pb_content_new' );

				if ( $content_textarea.length ) {
					$content_textarea_container = $content_textarea.closest( '.et-pb-option-container' );

					content = $content_textarea.html();

					$content_textarea.remove();

					$content_textarea_container.prepend( et_pb_content_html );

					setTimeout( function() {
						if ( typeof window.switchEditors !== 'undefined' )
							window.switchEditors.go( 'et_pb_content_new', et_get_editor_mode() );

						et_pb_set_content( 'et_pb_content_new', content );

						window.wpActiveEditor = 'et_pb_content_new';
					}, 300 );
				}

				setTimeout( function() {
					$this_el.find('select, input, textarea, radio').filter(':eq(0)').focus();
				}, 1 );

				return this;
			}
		} );

		ET_PageBuilder.BlockModuleView = window.wp.Backbone.View.extend( {

			className : function() {
				var className = 'et_pb_module_block';

				if ( typeof this.model.attributes.className !== 'undefined' ) {
					className += this.model.attributes.className;
				}

				return className;
			},

			template : _.template( $( '#et-builder-block-module-template' ).html() ),

			initialize : function() {
				this.listenTo( this.model, 'change:admin_label', this.renameModule );
				this.listenTo( this.model, 'change:et_pb_disabled', this.toggleDisabledClass );
				this.listenTo( this.model, 'change:et_pb_global_module', this.removeGlobal );
			},

			events : {
				'click .et-pb-settings' : 'showSettings',
				'click .et-pb-clone-module' : 'cloneModule',
				'click .et-pb-remove-module' : 'removeModule',
				'click .et-pb-unlock' : 'unlockModule',
				'contextmenu' : 'showRightClickOptions',
				'click' : 'hideRightClickOptions',
				'click' : 'setABTesting',
			},

			render : function() {
				var parent_views = ET_PageBuilder_Layout.getParentViews( this.model.get( 'parent' ) );

				this.$el.html( this.template( this.model.attributes ) );

				if ( typeof this.model.attributes.et_pb_global_module !== 'undefined' || ( typeof this.model.attributes.et_pb_template_type !== 'undefined' && 'module' === this.model.attributes.et_pb_template_type && 'global' === et_pb_options.is_global_template ) ) {
					this.$el.addClass( 'et_pb_global' );
				}

				if ( typeof this.model.get( 'et_pb_locked' ) !== 'undefined' && this.model.get( 'et_pb_locked' ) === 'on' ) {
					_.each( parent_views, function( parent ) {
						parent.$el.addClass( 'et_pb_children_locked' );
					} );
				}

				if ( typeof this.model.get( 'et_pb_parent_locked' ) !== 'undefined' && this.model.get( 'et_pb_parent_locked' ) === 'on' ) {
					this.$el.addClass( 'et_pb_parent_locked' );
				}

				if ( ET_PageBuilder_Layout.isModuleFullwidth( this.model.get( 'module_type' ) ) )
					this.$el.addClass( 'et_pb_fullwidth_module' );

				if ( typeof this.model.get( 'pasted_module' ) !== 'undefined' && this.model.get( 'pasted_module' ) ) {
					et_pb_handle_clone_class( this.$el );
				}

				if ( ET_PageBuilder_AB_Testing.is_active() ) {
					if ( ET_PageBuilder_AB_Testing.is_subject( this.model ) ) {
						this.$el.addClass( 'et_pb_ab_subject' );

						// Apply subject rank coloring
						ET_PageBuilder_AB_Testing.set_subject_rank_coloring( this );
					}

					if ( ET_PageBuilder_AB_Testing.is_goal( this.model ) ) {
						this.$el.addClass( 'et_pb_ab_goal' );
					}

					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'module', this.model ) ) {
						this.$el.addClass( 'et_pb_ab_no_permission' )
					}
				}

				return this;
			},

			cloneModule : function( event ) {
				var global_module_cid = '',
					clone_module,
					view_settings = {
						model      : this.model,
						view       : this.$el,
						view_event : event
					};

				event.preventDefault();

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				if ( this.isModuleLocked() ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'module' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}
				}

				if ( ( this.$el.closest( '.et_pb_section.et_pb_global' ).length || this.$el.closest( '.et_pb_row.et_pb_global' ).length ) && '' === et_pb_options.template_post_id ) {
					global_module_cid = et_pb_get_global_parent_cid( this );
				}

				clone_module = new ET_PageBuilder.RightClickOptionsView( view_settings, true );

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'cloned', 'module', this.model.get( 'admin_label' ) );

				clone_module.copy( event );

				clone_module.pasteAfter( event );

				if ( '' !== global_module_cid ) {
					et_pb_update_global_template( global_module_cid );
				}
			},

			renameModule : function() {
				this.$( '.et-pb-module-title' ).html( this.model.get( 'admin_label' ) );
			},

			removeGlobal : function() {
				if ( this.isModuleLocked() ) {
					return;
				}

				if ( typeof this.model.get( 'et_pb_global_module' ) === 'undefined' ) {
					this.$el.removeClass( 'et_pb_global' );
				}
			},

			toggleDisabledClass : function() {
				if ( typeof this.model.get( 'et_pb_disabled' ) !== 'undefined' && 'on' === this.model.get( 'et_pb_disabled' ) ) {
					this.$el.addClass( 'et_pb_disabled' );
				} else {
					this.$el.removeClass( 'et_pb_disabled' );
				}
			},

			showSettings : function( event ) {
				var that = this,
					modal_view,
					view_settings = {
						model : this.model,
						collection : this.collection,
						attributes : {
							'data-open_view' : 'module_settings'
						},
						triggered_by_right_click : this.triggered_by_right_click,
						do_preview : this.do_preview
					};

				if ( typeof event !== 'undefined' ) {
					event.preventDefault();
				}

				et_pb_close_all_right_click_options();

				if ( this.isModuleLocked() ) {
					return;
				}

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'module' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}
				}

				if ( typeof this.model.get( 'et_pb_global_module' ) !== 'undefined' && '' !== this.model.get( 'et_pb_global_module' ) ) {
					et_builder_get_global_module( view_settings );

					// Set marker variable to undefined after being used to prevent unwanted preview
					this.triggered_by_right_click = undefined;
					this.do_preview = undefined;
				} else {
					modal_view = new ET_PageBuilder.ModalView( view_settings );

					et_modal_view_rendered = modal_view.render();

					if ( false === et_modal_view_rendered ) {
						et_builder_load_backbone_templates( true );

						setTimeout( function() {
							that.showSettings();
						}, 500 );

						ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

						return;
					}

					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

					$('body').append( et_modal_view_rendered.el );
				}

				// set initial active tab for partially saved module templates.
				et_pb_open_current_tab();

				if ( ( typeof this.model.get( 'et_pb_global_parent' ) !== 'undefined' && '' !== this.model.get( 'et_pb_global_parent' ) ) || ( ET_PageBuilder_Layout.getView( this.model.get('cid') ).$el.closest( '.et_pb_global' ).length ) ) {
					$( '.et_pb_modal_settings_container' ).addClass( 'et_pb_saved_global_modal' );

					if ( 'module' === et_pb_options.global_module_type ) {
						et_pb_add_selective_sync_buttons( $( '.et_pb_modal_settings_container' ), et_pb_options.template_post_id );
						$( '.et_pb_modal_settings_container' ).addClass( 'et_pb_modal_selective_sync' );
					}
				}
			},

			removeModule : function( event ) {
				var global_module_cid = '';

				if ( this.isModuleLocked() ) {
					return;
				}

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
					return;
				}

				// Split Testing-related action
				if ( ET_PageBuilder_AB_Testing.is_active() ) {

					// Check for user permission and module status
					if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( this.model.get( 'cid' ), 'module' ) ) {
						ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
						return;
					}

					// Check for unremovable subject status
					if ( ET_PageBuilder_AB_Testing.is_unremovable_subject( this.model ) && ! ET_PageBuilder_Layout.get( 'forceRemove' ) ) {
						return;
					}
				}

				if ( event ) {
					event.preventDefault();

					if ( ( this.$el.closest( '.et_pb_section.et_pb_global' ).length || this.$el.closest( '.et_pb_row.et_pb_global' ).length ) && '' === et_pb_options.template_post_id ) {
						global_module_cid = et_pb_get_global_parent_cid( this );
					}
				}

				this.model.destroy();

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( 'removed', 'module', this.model.get( 'admin_label' ) );

				ET_PageBuilder_Layout.removeView( this.model.get('cid') );

				this.remove();

				// if single module is removed from the builder
				if ( event ) {
					ET_PageBuilder_Events.trigger( 'et-module:removed' );
				}

				if ( '' !== global_module_cid ) {
					et_pb_update_global_template( global_module_cid );
				}

				// Run Split Testing updater
				ET_PageBuilder_AB_Testing.update();
			},

			unlockModule : function( event ) {
				event.preventDefault();

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				var this_el = this,
					$parent = this_el.$el.closest('.et_pb_module_block'),
					request = et_pb_user_lock_permissions(),
					parent_views;

				request.done( function ( response ) {
					if ( true === response ) {
						$parent.removeClass('et_pb_locked');

						// Add attribute to shortcode
						this_el.options.model.attributes.et_pb_locked = 'off';

						parent_views = ET_PageBuilder_Layout.getParentViews( this_el.model.get('parent') );

						_.each( parent_views, function( view, key ) {
							if ( ! ET_PageBuilder_Layout.isChildrenLocked( view.model.get( 'cid' ) ) ) {
								view.$el.removeClass('et_pb_children_locked');
							}
						} );

						// Enable history saving and set meta for history
						ET_PageBuilder_App.allowHistorySaving( 'unlocked', 'module', this_el.options.model.get( 'admin_label' ) );

						// Rebuild shortcodes
						ET_PageBuilder_App.saveAsShortcode();
					} else {
						alert( et_pb_options.locked_module_permission_alert );
					}
				});
			},

			isModuleLocked : function() {
				if ( 'on' === this.model.get( 'et_pb_locked' ) || 'on' === this.model.get( 'et_pb_parent_locked' ) ) {
					return true;
				}

				return false;
			},

			showRightClickOptions : function( event ) {
				event.preventDefault();

				var et_right_click_options_view,
					view_settings = {
						model      : this.model,
						view       : this.$el,
						view_event : event
					};

				et_right_click_options_view = new ET_PageBuilder.RightClickOptionsView( view_settings );
			},

			hideRightClickOptions : function( event ) {
				event.preventDefault();

				et_pb_close_all_right_click_options();
			},

			setABTesting : function ( event ) {
				event.preventDefault();
				event.stopPropagation();

				ET_PageBuilder_AB_Testing.set( this, event );
			}
		} );

		ET_PageBuilder.HelpView = window.wp.Backbone.View.extend({
			tagName: 'div',

			id: 'et-builder-help',

			className: 'et_pb_modal_settings',

			template : _.template( $( '#et-builder-help-template' ).html() ),

			isOSX: navigator.userAgent.indexOf('Mac OS X') != -1,

			renderKbd: function(kbd) {
				var key = kbd;

				if (key === 'super') {
					if (this.isOSX) {
						key = 'cmd';
					} else {
						key = 'ctrl';
					}
				}

				return $('<kbd />', {
					class: 'key-' + key,
				}).html(key);
			},

			render: function() {
				var thisClass = this;

				var $thisModal = this.$el;

				et_pb_close_all_right_click_options();

				$thisModal.html( this.template() );

				var $shortcutTab = $thisModal.find('.et-pb-shortcuts-tab');

				_.each(et_pb_help_options.shortcuts, function(shortcutSets){
					_.each(shortcutSets, function(shortcutSet) {
						if (_.isUndefined(shortcutSet.title)) {
							// Define item
							var $shortcutItem = $('<p />', {
								class: 'et-pb-shortcut-item'
							});

							// Build shortcut keys
							var $shortcutKeys = $('<span />', {
								class: 'et-pb-shortcut-keys'
							});

							_.each(shortcutSet.kbd, function(shortcutKey, shortcutKeyIndex) {
								// Append + divider
								if (shortcutKeyIndex > 0) {
									$shortcutKeys.append(' + ');
								}

								if (_.isArray(shortcutKey)) {
									_.each(shortcutKey, function(shortcutOption, shortcutOptionIndex) {
										if (shortcutOptionIndex > 0) {
											$shortcutKeys.append(' / ');
										}

										$shortcutKeys.append(
											$('<kbd />').html(shortcutOption)
										);
									});
								} else {
									$shortcutKeys.append(
										// $('<kbd />').html(shortcutKey)
										thisClass.renderKbd(shortcutKey)
									);
								}
							});

							// Append shortcut keys
							$shortcutItem.append($shortcutKeys);

							// Append description
							$shortcutItem.append(
								$('<span />', {
									class: 'et-pb-shortcut-desc'
								}).html(shortcutSet.desc)
							);

							// Append  Item
							$shortcutTab.append($shortcutItem);
						} else {
							$shortcutTab.append(
								$('<h2 />', {class: 'et-pb-shortcut-subtitle'}).html(shortcutSet.title)
							);
						}
					});
				});

				return this;
			}
		});

		ET_PageBuilder.RightClickOptionsView = window.wp.Backbone.View.extend( {

			tagName : 'div',

			id : 'et-builder-right-click-controls',

			template : _.template( $('#et-builder-right-click-controls-template').html() ),

			events : {
				'click .et-pb-right-click-rename' : 'rename',
				'click .et-pb-right-click-start-ab-testing' : 'startABTesting',
				'click .et-pb-right-click-end-ab-testing' : 'endABTesting',
				'click .et-pb-right-click-save-to-library' : 'saveToLibrary',
				'click .et-pb-right-click-undo' : 'undo',
				'click .et-pb-right-click-redo' : 'redo',
				'click .et-pb-right-click-disable' : 'disable',
				'click .et_pb_disable_on_option' : 'disable_device',
				'click .et-pb-right-click-lock' : 'lock',
				'click .et-pb-right-click-collapse' : 'collapse',
				'click .et-pb-right-click-copy' : 'copy',
				'click .et-pb-right-click-paste-after' : 'pasteAfter',
				'click .et-pb-right-click-paste-app' : 'pasteApp',
				'click .et-pb-right-click-paste-column' : 'pasteColumn',
				'click .et-pb-right-click-preview' : 'preview',
				'click .et-pb-right-click-disable-global' : 'disableGlobal'
			},

			initialize : function( attributes, skip_render ) {
				var skip_render                       = _.isUndefined( skip_render ) ? false : skip_render,
					allowed_library_clipboard_content;

				this.type                             = this.options.model.attributes.type;
				this.et_pb_has_storage_support        = et_pb_has_storage_support();
				this.has_compatible_clipboard_content = ET_PB_Clipboard.get( this.getClipboardType() );
				this.history_noun                     = this.type === 'row_inner' ? 'row' : this.type;

				if ( ET_PageBuilder_App.isLoading ) {
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting() ) {
					return;
				}

				// Divi Library adjustment
				if ( et_pb_options.is_divi_library === '1' && this.has_compatible_clipboard_content !== false ) {
					// There are four recognized layout type: layout, section, row, module
					switch( et_pb_options.layout_type ) {
						case 'module' :
							allowed_library_clipboard_content = [];
							break;
						case 'row' :
							allowed_library_clipboard_content = ['module'];
							break;
						case 'section' :
							allowed_library_clipboard_content = ['module', 'row'];
							break;
						default :
							allowed_library_clipboard_content = ['module', 'row', 'section'];
							break;
					}

					// If current clipboard type isn't allowed, disable pasteAfter
					if ( $.inArray( this.type, allowed_library_clipboard_content ) == -1 ) {
						this.has_compatible_clipboard_content = false;
					}
				}

				// Enable right options control rendering to be skipped
				if ( skip_render === false ) {
					this.render();
				}
			},

			render : function() {
				var $parent = $( this.options.view ),
					$options_wrap = this.$el.html( this.template() ),
					view_offset = this.options.view.offset(),
					parent_offset_x = this.options.view_event.pageX - view_offset.left - 100,
					parent_offset_y = this.options.view_event.pageY - view_offset.top;

				// close other options, if there's any
				this.closeAllRightClickOptions();

				// Prevent recursive right click options
				if ( $( this.options.view_event.toElement ).is('#et-builder-right-click-controls a')  ) {
					return;
				}

				// Don't display empty right click options
				if ( $options_wrap.find('li').length < 1 ) {
					return;
				}

				// Append options to the page
				$parent.append( $options_wrap );

				// Fixing options' position and animating it
				$options_wrap.find('.options').css({
					'top' : parent_offset_y,
					'left' : parent_offset_x,
					'margin-top': ( 0 - $options_wrap.find('.options').height() - 40 ),
				}).animate({
					'margin-top': ( 0 - $options_wrap.find('.options').height() - 10 ),
					'opacity' : 1
				}, 300 );

				// Add full screen page overlay (right/left click anywhere outside builder to close options)
				$('#et_pb_layout').prepend('<div id="et_pb_layout_right_click_overlay" />');
			},

			closeAllRightClickOptions : function() {
				et_pb_close_all_right_click_options();

				return false;
			},

			rename : function( event ) {
				event.preventDefault();

				var $parent = this.$el.parent(),
					cid = this.options.model.attributes.cid;

				et_pb_create_prompt_modal( 'rename_admin_label', cid );

				// close the click right options
				this.closeAllRightClickOptions();
			},

			startABTesting : function ( event ) {

				// Close right click options UI
				this.closeAllRightClickOptions();

				// Turn on Split Testing state
				ET_PageBuilder_AB_Testing.toggle_status( true );

				// Disable publish button
				ET_PageBuilder_App.disable_publish = true;

				$( '#publish' ).addClass( 'disabled' );

				// Check DB existence
				ET_PageBuilder_AB_Testing.check_create_db();

				// Turn on Split testing subject selection mode
				ET_PageBuilder_App.is_selecting_ab_testing_subject = true;

				// Adding nescesarry class for Split testing subject selection mode's UI
				$( '#et_pb_layout' ).addClass( 'et_pb_select_ab_testing_subject' );
			},

			endABTesting : function ( event ) {
				// Close right click options UI
				this.closeAllRightClickOptions();

				// Set split test to off
				ET_PageBuilder_AB_Testing.toggle_status( false );

				// Turn off Split testing subject selection mode
				ET_PageBuilder_App.is_selecting_ab_testing_subject = false;

				// Check against "on to off" or "off to off" state
				if ( ET_PageBuilder_AB_Testing.count_subjects() > 0 ) {
					et_pb_create_prompt_modal( 'turn_off_ab_testing' );
				}
			},

			disableGlobal : function ( event ) {
				event.preventDefault();

				// Close right click options UI
				this.closeAllRightClickOptions();

				// Remove global attributes from the module
				ET_PageBuilder_Layout.removeGlobalAttributes( this );

				// Update content and reinit layout
				et_reinitialize_builder_layout();
			},

			saveToLibrary : function ( event ) {
				event.preventDefault();

				var model          = this.options.model,
					type           = model.attributes.type,
					view_settings  = {
						model : model,
						collection : ET_PageBuilder_Modules,
						attributes : {
							'data-open_view' : 'module_settings'
						}
					};

				// Close right click options UI
				this.closeAllRightClickOptions();

				if ( ET_PageBuilder_AB_Testing.is_active() && ( ET_PageBuilder_AB_Testing.is_split_test_item( model ) || type === 'app' ) ) {
					ET_PageBuilder_AB_Testing.alert( 'cannot_save_' + type + '_layout_has_ab_testing' );
					return;
				}

				if ( this.type === 'app' ) {
					// Init save current page to library modal view
					et_pb_create_prompt_modal( 'save_layout' );
				} else {
					// Init modal view
					modal_view = new ET_PageBuilder.ModalView( view_settings );

					// Append modal view
					$('body').append( modal_view.render().el );

					// set initial active tab for partially saved module templates.
					et_pb_open_current_tab();

					// Init save template modal view
					modal_view.saveTemplate( event );
				}
			},

			undo : function( event ) {
				event.preventDefault();

				// Undoing...
				ET_PageBuilder_App.undo( event );

				// Close right click options UI
				this.closeAllRightClickOptions();
			},

			redo : function( event ) {
				event.preventDefault();

				// Redoing...
				ET_PageBuilder_App.redo( event );

				// Close right click options UI
				this.closeAllRightClickOptions();
			},

			disable : function( event ) {
				event.preventDefault();

				var $this_button = $( event.target ).hasClass( 'et-pb-right-click-disable' ) ? $( event.target ) : $( event.target ).closest( 'a' ),
					this_options_container = $this_button.closest( 'li' ).find( 'span.et_pb_disable_on_options' ),
					single_options = this_options_container.find( 'span.et_pb_disable_on_option' ),
					is_all_disabled = typeof this.options.model.attributes.et_pb_disabled !== 'undefined' && 'on' === this.options.model.attributes.et_pb_disabled ? true : false,
					disabled_on = typeof this.options.model.attributes.et_pb_disabled_on !== 'undefined' ? this.options.model.attributes.et_pb_disabled_on : '',
					disabled_on_array,
					i,
					device;

				$this_button.addClass( 'et_pb_right_click_hidden' );

				this_options_container.addClass( 'et_pb_right_click_visible' );

				// backward compatibility with old option
				if ( is_all_disabled ) {
					single_options.addClass( 'et_pb_disable_on_active' );
				} else if ( '' !== disabled_on ) {
					disabled_on_array = disabled_on.split('|');
					i = 0,
					device = 'phone';

					single_options.each( function() {
						var this_option = $( this );

						if ( this_option.hasClass( 'et_pb_disable_on_' + device ) && 'on' === disabled_on_array[ i ] ) {
							this_option.addClass( 'et_pb_disable_on_active' );
						}

						i++;
						device = 1 === i ? 'tablet' : 'desktop';
					} );
				}

				return false;
			},

			disable_device : function( event ) {
				var $this_button = $( event.target ),
					this_option = $( this ),
					new_option_state = $this_button.hasClass( 'et_pb_disable_on_active' ) ? 'off' : 'on',
					disabled_on = typeof this.options.model.attributes.et_pb_disabled_on !== 'undefined' ? this.options.model.attributes.et_pb_disabled_on : '',
					$parent = this.$el.parent(),
					history_verb,
					disabled_on_array,
					option_index,
					history_addition;

				// determine which option should be updated, Phone, Tablet or Desktop.
				if ( $this_button.hasClass( 'et_pb_disable_on_phone' ) ) {
					option_index = 0;
					history_addition = 'phone';
				} else if ( $this_button.hasClass( 'et_pb_disable_on_tablet' ) ) {
					option_index = 1;
					history_addition = 'tablet';
				} else {
					option_index = 2;
					history_addition = 'desktop';
				}

				if ( '' !== disabled_on ) {
					disabled_on_array = disabled_on.split('|');
				} else {
					disabled_on_array = ['','',''];
				}

				disabled_on_array[ option_index ] = new_option_state;

				this.options.model.attributes.et_pb_disabled_on = disabled_on_array[0] + '|' + disabled_on_array[1] + '|' + disabled_on_array[2];

				if ( 'on' === disabled_on_array[0] && 'on' === disabled_on_array[1] && 'on' === disabled_on_array[2] ) {
					parent_background_color = $parent.css('backgroundColor');

					$parent.addClass('et_pb_disabled');

					// Add attribute to shortcode
					this.options.model.attributes.et_pb_disabled = 'on';
					history_verb = 'disabled';
				} else {
					// toggle et_pb_disabled class
					$parent.removeClass( 'et_pb_disabled' );

					// Remove attribute to shortcode
					this.options.model.attributes.et_pb_disabled = 'off';
					history_verb = 'off' === new_option_state ? 'enabled' : 'disabled';
				}

				$this_button.toggleClass( 'et_pb_disable_on_active' );

				// Update global module
				this.updateGlobalModule();

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( history_verb, this.history_noun, undefined, history_addition );

				// Rebuild shortcodes
				ET_PageBuilder_App.saveAsShortcode();

				return false;
			},

			lock : function( event ) {
				event.preventDefault();

				var $parent = this.$el.parent();

				// toggle et_pb_locked class
				if ( $parent.hasClass('et_pb_locked') ) {
					this.unlockItem();

					// Enable history saving and set meta for history
					ET_PageBuilder_App.allowHistorySaving( 'unlocked', this.history_noun );
				} else {
					this.lockItem();

					// Enable history saving and set meta for history
					ET_PageBuilder_App.allowHistorySaving( 'locked', this.history_noun );
				}

				// close the click right options
				this.closeAllRightClickOptions();

				// Rebuild shortcodes
				ET_PageBuilder_App.saveAsShortcode();
			},

			unlockItem : function() {
				var this_el = this,
					$parent = this_el.$el.parent(),
					request = et_pb_user_lock_permissions(),
					children_views,
					parent_views;

				request.done( function ( response ) {
					if ( true === response ) {
						$parent.removeClass('et_pb_locked');

						// Add attribute to shortcode
						this_el.options.model.attributes.et_pb_locked = 'off';

						if ( 'module' !== this_el.options.model.get( 'type' ) ) {
							children_views = ET_PageBuilder_Layout.getChildrenViews( this_el.model.get('cid') );

							_.each( children_views, function( view, key ) {
								view.$el.removeClass('et_pb_parent_locked');
								view.model.set( 'et_pb_parent_locked', 'off', { silent : true } );
							} );
						}

						if ( 'section' !== this_el.options.model.get( 'type' ) ) {
							parent_views = ET_PageBuilder_Layout.getParentViews( this_el.model.get( 'parent' ) );

							_.each( parent_views, function( view, key ) {
								if ( ! ET_PageBuilder_Layout.isChildrenLocked( view.model.get( 'cid' ) ) ) {
									view.$el.removeClass('et_pb_children_locked');
								}
							} );
						}

						// Enable history saving and set meta for history
						ET_PageBuilder_App.allowHistorySaving( 'unlocked', this_el.history_noun );

						// Rebuild shortcodes
						ET_PageBuilder_App.saveAsShortcode();

						// Update global module
						this_el.updateGlobalModule();
					} else {
						alert( et_pb_options.locked_item_permission_alert );
					}
				});
			},

			lockItem : function() {
				var this_el = this,
					$parent = this_el.$el.parent(),
					request = et_pb_user_lock_permissions(),
					children_views,
					parent_views;

				request.done( function ( response ) {
					if ( true === response ) {
						$parent.addClass('et_pb_locked');

						// Add attribute to shortcode
						this_el.options.model.attributes.et_pb_locked = 'on';

						if ( 'module' !== this_el.options.model.get( 'type' ) ) {
							children_views = ET_PageBuilder_Layout.getChildrenViews( this_el.model.get('cid') );

							_.each( children_views, function( view, key ) {
								view.$el.addClass('et_pb_parent_locked');
								view.model.set( 'et_pb_parent_locked', 'on', { silent : true } );
							} );
						}

						if ( 'section' !== this_el.options.model.get( 'type' ) ) {
							parent_views = ET_PageBuilder_Layout.getParentViews( this_el.model.get( 'parent' ) );

							_.each( parent_views, function( view, key ) {
								view.$el.addClass( 'et_pb_children_locked' );
							} );
						}

						// Enable history saving and set meta for history
						ET_PageBuilder_App.allowHistorySaving( 'locked', this_el.history_noun );

						// Rebuild shortcodes
						ET_PageBuilder_App.saveAsShortcode();

						// Update global module
						this_el.updateGlobalModule();
					} else {
						alert( et_pb_options.locked_item_permission_alert );
					}
				});
			},

			collapse : function( event ) {
				event.preventDefault();

				var $parent = this.$el.parent(),
					cid = this.options.model.attributes.cid,
					history_verb;

				$parent.toggleClass('et_pb_collapsed');

				if ( $parent.hasClass('et_pb_collapsed') ) {
					// Add attribute to shortcode
					this.options.model.attributes.et_pb_collapsed = 'on';
					history_verb = 'collapsed';
				} else {
					// Add attribute to shortcode
					this.options.model.attributes.et_pb_collapsed = 'off';
					history_verb = 'expanded';
				}

				// Carousel effect for split testing subject
				if ( ET_PageBuilder_AB_Testing.is_active() && this.model.get( 'et_pb_ab_subject' ) === 'on' ) {
					ET_PageBuilder_AB_Testing.subject_carousel( cid );
				}

				// Update global module
				this.updateGlobalModule();

				// close the click right options
				this.closeAllRightClickOptions();

				// Enable history saving and set meta for history
				ET_PageBuilder_App.allowHistorySaving( history_verb, this.history_noun );

				// Rebuild shortcodes
				ET_PageBuilder_App.saveAsShortcode();
			},

			copy : function( event, keepEvent ) {

				if ( ! keepEvent ) {
					event.preventDefault();
				}

				var module_attributes = _.clone( this.model.attributes ),
					type              = module_attributes.type,
					clipboard_content;

				// Normalize row_inner as row. Specialty's section row is detected as row_inner
				// but selector-wise, there's no .et_pb_row_inner. It uses the same .et_pb_row
				if ( type === 'row_inner' ) {
					type = 'row';
				}

				// Delete circular structure element carried by default by specialty section's row inner
				if ( ! _.isUndefined( module_attributes.view ) ) {
					delete module_attributes.view;
				}

				// Delete appendAfter element, its leftover can cause misunderstanding on rendering UI
				if ( ! _.isUndefined( module_attributes.appendAfter ) ) {
					delete module_attributes.appendAfter;
				}

				// append childview's data to mobile_attributes for row and section
				if ( type === 'row' || type === 'section' ) {
					module_attributes.childviews = this.getChildViews( module_attributes.cid );
				}

				delete module_attributes.className;
				delete module_attributes.et_pb_locked;

				// Set clipboard content
				clipboard_content = JSON.stringify( module_attributes );

				// Save content to clipboard
				ET_PB_Clipboard.set( this.getClipboardType(), clipboard_content );

				// close the click right options
				this.closeAllRightClickOptions();
			},

			pasteAfter : function( event, parent, clipboard_type, has_cloned_cid, keepEvent, noHistory ) {
				if ( ! keepEvent ) {
					event.preventDefault();
				}

				var parent            = _.isUndefined( parent ) ? this.model.get( 'parent' ) : parent,
					clipboard_type    = _.isUndefined( clipboard_type ) ? this.getClipboardType() : clipboard_type,
					clipboard_content,
					has_cloned_cid    = _.isUndefined( has_cloned_cid ) ? true : has_cloned_cid;

				// Get clipboard content
				clipboard_content = ET_PB_Clipboard.get( clipboard_type );
				clipboard_content = JSON.parse( clipboard_content );

				// If current clipboard content is Split testing subject, assign new subject ID
				if ( ! _.isUndefined( clipboard_content.et_pb_ab_subject ) || 'on' === clipboard_content.et_pb_ab_subject ) {
					clipboard_content.et_pb_ab_subject_id = ET_PageBuilder_AB_Testing.get_subject_id();
				}

				if ( has_cloned_cid ) {
					clipboard_content.cloned_cid = this.model.get( 'cid' );
				}

				// Paste views recursively
				this.setPasteViews( clipboard_content, parent, 'main_parent' );

				// Carousel effect for split testing subject
				if ( ET_PageBuilder_AB_Testing.is_active() && ( clipboard_content.type === 'row' || clipboard_content.type === 'row_inner' || clipboard_content.type === 'section' ) && clipboard_content.et_pb_ab_subject === 'on' ) {
					ET_PageBuilder_AB_Testing.subject_carousel( clipboard_content.cid );
				}

				// Trigger events
				ET_PageBuilder_Events.trigger( 'et-advanced-module:updated' );
				ET_PageBuilder_Events.trigger( 'et-advanced-module:saved' );

				// Update global module
				this.updateGlobalModule();

				// close the click right options
				this.closeAllRightClickOptions();

				// Enable history saving and set meta for history
				// pasteAfter can be used for clone, so only use copied if history verb being used is default
				if ( ET_PageBuilder_Visualize_Histories.verb === 'did' && ! noHistory ) {
					ET_PageBuilder_App.allowHistorySaving( 'copied', this.history_noun );
				}

				if ( ! keepEvent ) {
					// Rebuild shortcodes
					ET_PageBuilder_App.saveAsShortcode();
				}
			},

			pasteApp : function( event ) {
				event.preventDefault();

				// Get last' section model
				var sections     = ET_PageBuilder_Modules.where({ 'type' : 'section' }),
					last_section = _.last( sections );

				// Set last section as this.model and this.options.model so setPasteViews() can parse the clipboard correctly
				this.model = last_section;
				this.options.model = last_section;

				// Paste Item
				this.pasteAfter( event, undefined, 'et_pb_clipboard_section', false );
			},

			pasteColumn : function( event ) {
				event.preventDefault();

				var parent         = this.model.get( 'cid' ),
					clipboard_type = this.model.get('type') === 'section' ? 'et_pb_clipboard_module_fullwidth' : 'et_pb_clipboard_module';

				// Paste item
				this.pasteAfter( event, parent, clipboard_type, false );
			},

			getClipboardType : function() {
				var type              = this.model.attributes.type,
					module_type        = _.isUndefined( this.model.attributes.module_type ) ? this.model.attributes.type : this.model.attributes.module_type,
					clipboard_key     = 'et_pb_clipboard_' + type,
					fullwidth_prefix  = 'et_pb_fullwidth';

				// Added fullwidth prefix
				if ( module_type.substr( 0, fullwidth_prefix.length ) === fullwidth_prefix ) {
					clipboard_key += '_fullwidth';
				}

				return clipboard_key;
			},

			getChildViews : function( parent ) {
				var this_el = this,
					views = ET_PageBuilder_Modules.models,
					child_attributes,
					child_views = [];

				_.each( views, function( view, key ) {
					if ( view.attributes.parent === parent ) {
						child_attributes = view.attributes;

						// Delete circular structure element carried by default by specialty section's row inner
						if ( ! _.isUndefined( child_attributes.view ) ) {
							delete child_attributes.view;
						}

						// Delete appendAfter element, its leftover can cause misunderstanding on rendering UI
						if ( ! _.isUndefined( child_attributes.appendAfter ) ) {
							delete child_attributes.appendAfter;
						}

						child_attributes.created = 'manually';

						// Append grand child views, if there's any
						child_attributes.childviews = this_el.getChildViews( view.attributes.cid );
						child_views.push( child_attributes );
					}
				} );

				return child_views;
			},

			setPasteViews : function( view, parent, is_main_parent ) {
				var this_el    = this,
					cid        = ET_PageBuilder_Layout.generateNewId(),
					view_index = this.model.collection.indexOf( this.model ),
					childviews = ( ! _.isUndefined( view.childviews ) && _.isArray( view.childviews ) ) ? view.childviews : false,
					global_module_elements = [ 'et_pb_global_parent', 'global_parent_cid' ];

				// Add newly generated cid and parent to the pasted view
				view.cid    = cid;
				view.parent = parent;

				if ( typeof is_main_parent !== 'undefined' && 'main_parent' === is_main_parent ) {
					view.pasted_module = true;
				} else {
					view.pasted_module = false;
				}

				// update global parent attribute when pasting the module from Global Row or Section.
				if ( !  _.isUndefined( view.et_pb_global_parent ) && '' !== view.et_pb_global_parent ) {
					view.et_pb_global_parent = et_pb_get_global_parent_cid( ET_PageBuilder_Layout.getView( parent ) );
				}

				// Set new global_parent_cid for pasted element
				if ( ! _.isUndefined( view.et_pb_global_module ) && _.isUndefined( view.global_parent_cid ) && _.isUndefined( this.set_global_parent_cid ) ) {
					this.global_parent_cid = cid;
					this.set_global_parent_cid = true;
				}

				if ( ! _.isUndefined( view.global_parent_cid ) ) {
					view.global_parent_cid = this.global_parent_cid;
				}

				// If the view is pasted inside global module, inherit its global module child attributes
				_.each( global_module_elements, function( global_module_element ) {
					if ( ! _.isUndefined( this_el.options.model.get( global_module_element ) ) && _.isUndefined( view[ global_module_element ] ) ) {
						view[ global_module_element ] = this_el.options.model.get( global_module_element );
					}
				} );

				// Remove template type leftover. Template type is used by Divi Library to remove item's settings and clone button
				if ( ! _.isUndefined( view.et_pb_template_type ) ) {
					delete view.et_pb_template_type;
				}

				// If current view is Split testing subject, assign new subject ID
				if ( ! _.isUndefined( view.et_pb_ab_subject ) || 'on' === view.et_pb_ab_subject ) {
					view.et_pb_ab_subject_id = ET_PageBuilder_AB_Testing.get_subject_id();
				}

				// Delete unused childviews
				delete view.childviews;

				// add default Admin Label if not defined
				if ( _.isUndefined( view.admin_label ) ) {
					view.admin_label = ET_PageBuilder_Layout.getDefaultAdminLabel( view.module_type );
				}

				// Add view to collections
				this.model.collection.add( view, { at : view_index } );

				// If current view has childviews (row & module), repeat the process above recursively
				if ( childviews ) {
					_.each( childviews, function( childview ){
						this_el.setPasteViews( childview, cid );
					});
				};
			},

			updateGlobalModule : function () {
				var global_module_cid;

				if ( ET_PageBuilder_Layout.is_global( this.model ) ) {
					global_module_cid = this.options.model.get( 'cid' );
				} else if ( ET_PageBuilder_Layout.is_global_children( this.model ) ) {
					global_module_cid = this.options.model.get( 'global_parent_cid' );
				}

				if ( ! _.isUndefined( global_module_cid ) ) {
					et_pb_update_global_template( global_module_cid );
				}
			},

			hasOption : function( option_name ) {
				var cid          = typeof this.model.get === 'function' ? this.model.get( 'cid' ) : false,
					has_option   = false,
					type         = this.options.model.attributes.type,
					is_ab_active = ET_PageBuilder_AB_Testing.is_active(),
					is_ab_goal   = is_ab_active ? ET_PageBuilder_AB_Testing.is_goal( this.model ) : false,
					is_ab_goal_children = is_ab_active ? ET_PageBuilder_AB_Testing.is_goal_children( this.model ) : false,
					has_ab_goal  = is_ab_active ? ET_PageBuilder_AB_Testing.has_goal( this.model ) : false,
					is_ab_subject= is_ab_active ? ET_PageBuilder_AB_Testing.is_subject( this.model ) : false,
					is_ab_subject_children = is_ab_active ? ET_PageBuilder_AB_Testing.is_subject_children( this.model ) : false,
					is_ab_allowed_change = is_ab_active ? ET_PageBuilder_AB_Testing.is_user_has_permission( cid, 'right_click_change' ) : true,
					is_ab_allowed_copy = is_ab_active ? ET_PageBuilder_AB_Testing.is_user_has_permission( cid, 'copy' ) : true,
					is_ab_allowed_paste = is_ab_active ? ET_PageBuilder_AB_Testing.is_user_has_permission( cid, 'paste' ) : true;

				switch( option_name ) {
					case "rename" :
							if ( this.hasOptionSupport( [ "module", "section", "row_inner", "row" ] ) &&
								 this.options.model.attributes.et_pb_locked !== "on" &&
								 is_ab_allowed_change ) {
								has_option = true;
							}
						break;
					case "save-to-library" :
							if ( this.hasOptionSupport( [ "app", "section", "row_inner", "row", "module" ] ) &&
								 ! ET_PageBuilder_Layout.is_global( this.options.model ) &&
								 ! ET_PageBuilder_Layout.is_global_children( this.options.model ) &&
								 this.options.model.attributes.et_pb_locked !== "on" &&
								 ! ( ET_PageBuilder_AB_Testing.is_active() && ( ET_PageBuilder_AB_Testing.is_split_test_item( this.options.model ) || type === 'app' ) ) &&
								 et_pb_options.is_divi_library !== "1" ) {
								has_option = true;
							}
						break;
					case "start-ab-testing" :
							if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
								 ! is_ab_active ) {
								has_option = true;
							}
						break;
					case "end-ab-testing" :
							if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
								( is_ab_subject || is_ab_goal || is_ab_subject_children || is_ab_goal_children ) &&
								is_ab_active ) {
								has_option = true;
							}
						break;
					case "disable-global" :
							if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
								 ( ET_PageBuilder_Layout.is_global( this.options.model ) || ET_PageBuilder_Layout.is_global_children( this.options.model) )
							) {
								has_option = true;
							}
						break;
					case "undo" :
							if ( this.hasOptionSupport( [ "app", "section", "row_inner", "row", "column", "column_inner", "module" ] ) &&
								 this.hasUndo() ) {
								has_option = true;
							}
						break;
					case "redo" :
							if ( this.hasOptionSupport( [ "app", "section", "row_inner", "row", "column", "column_inner", "module" ] ) &&
								 this.hasRedo() ) {
								has_option = true;
							}
						break;
					case "disable" :
							if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
								 this.options.model.attributes.et_pb_locked !== "on" &&
								 this.hasDisabledParent() === false &&
								 _.isUndefined( this.model.attributes.et_pb_skip_module ) &&
								 is_ab_allowed_change ) {
								has_option = true;
							}
						break;
					case "lock" :
							if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
								 _.isUndefined( this.model.attributes.et_pb_skip_module ) &&
								 is_ab_allowed_change ) {
								has_option = true;
							}
						break;
					case "collapse" :
							if ( this.hasOptionSupport( [ "section", "row_inner", "row" ] ) &&
								 this.options.model.attributes.et_pb_locked !== "on" &&
								 ! ( ET_PageBuilder_AB_Testing.is_active() && this.options.model.get( 'et_pb_ab_subject' ) === "on" && ( this.options.model.get( 'et_pb_collapsed' ) === "off" || _.isUndefined( this.options.model.get( 'et_pb_collapsed' ) ) )  ) &&
								 _.isUndefined( this.model.attributes.et_pb_skip_module ) ) {
								has_option = true;
							}
						break;
					case "copy" :
							if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
								 this.et_pb_has_storage_support &&
								 _.isUndefined( this.model.attributes.et_pb_skip_module ) &&
								 is_ab_allowed_copy &&
								 ! is_ab_goal &&
								 ! has_ab_goal ) {
								has_option = true;
							}
						break;
					case "paste-after" :
							if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
								 this.et_pb_has_storage_support &&
								 this.has_compatible_clipboard_content &&
								 is_ab_allowed_paste ) {
								has_option = true;
							}
						break;
					case "paste-app" :
							if ( this.hasOptionSupport( [ "app" ] ) &&
								 this.et_pb_has_storage_support &&
								 ET_PB_Clipboard.get( "et_pb_clipboard_section" ) ) {
								has_option = true;
							}
						break;
					case "paste-column" :
							if ( ! _.isUndefined( this.model.attributes.is_insert_module ) &&
								( ( ( this.type === "column" || this.type == "column_inner" ) && ET_PB_Clipboard.get( "et_pb_clipboard_module" ) ) || ( this.type === "section" && ET_PB_Clipboard.get( "et_pb_clipboard_module_fullwidth" ) ) ) &&
								this.et_pb_has_storage_support ) {
								has_option = true;
							}
						break;
					case "preview" :
							if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
								this.options.model.attributes.et_pb_locked !== "on" ) {
								has_option = true;
							}
						break;
				}

				return has_option;
			},

			hasOptionSupport : function( whitelisted_types ) {
				if ( _.isUndefined( _.findWhere( whitelisted_types, this.type ) ) ) {
					return false;
				}

				return true;
			},

			hasUndo : function() {
				return ET_PageBuilder_App.hasUndo();
			},

			hasRedo : function() {
				return ET_PageBuilder_App.hasRedo();
			},

			hasDisabledParent : function() {
				var parent_view = ET_PageBuilder_Layout.getView( this.model.attributes.parent ),
					parent_views = {},
					has_disabled_parents = false;

				// Loop until parent_view is undefined (reaches section)
				while ( ! _.isUndefined( parent_view  ) ) {
					// Check whether current parent is disabled or not
					if ( ! _.isUndefined( parent_view.model.attributes.et_pb_disabled ) && parent_view.model.attributes.et_pb_disabled === "on" ) {
						has_disabled_parents = true;
					}

					// Append views to object
					parent_views[parent_view.model.attributes.cid] = parent_view;

					// Refresh parent_view for new loop
					parent_view = ET_PageBuilder_Layout.getView( parent_view.model.attributes.parent );
				}

				return has_disabled_parents;
			},

			preview : function( event ) {
				event.preventDefault();

				// Get item's view
				var view = ET_PageBuilder_Layout.getView( this.model.get( 'cid' ) );

				// Close all right click options
				this.closeAllRightClickOptions();

				// Tell view that it is initiated from right click options so it can tell modalView
				view.triggered_by_right_click = true;

				// Tell modal view that this instance is intended for previewing
				// This is specifically needed for global module
				view.do_preview = true;

				// Display ModalView
				view.showSettings( event );

				// Emulate preview clicking
				$('.et-pb-modal-preview-template').trigger( 'click' );
			}
		} );

		ET_PageBuilder.visualizeHistoriesView = window.wp.Backbone.View.extend( {

			el : '#et-pb-histories-visualizer',

			template : _.template( $('#et-builder-histories-visualizer-item-template').html() ),

			events : {
				'click li' : 'rollback'
			},

			verb : 'did',

			noun : 'module',

			noun_alias : undefined,

			addition : '',

			getItemID : function( model ) {
				return '#et-pb-history-' + model.get( 'timestamp' );
			},

			getVerb : function() {
				var verb = this.verb;

				if ( ! _.isUndefined( et_pb_options.verb[verb] ) ) {
					verb = et_pb_options.verb[verb];
				}

				return verb;
			},

			getNoun : function() {
				var noun = this.noun;

				if ( ! _.isUndefined( this.noun_alias ) ) {
					noun = this.noun_alias;
				} else if ( ! _.isUndefined( et_pb_options.noun[noun] ) ) {
					noun = et_pb_options.noun[noun];
				}

				return noun;
			},

			getAddition : function() {
				var addition = this.addition;

				if ( ! _.isUndefined( et_pb_options.addition[addition] ) ) {
					addition = et_pb_options.addition[addition];
				}

				return addition;
			},

			addItem : function( model ) {
				// Setting the passed model as class' options so the template can be rendered correctly
				this.options = model;

				// Prepend history item to container
				this.$el.prepend( this.template() );

				// Fix max-height for history visualizer
				this.setHistoriesHeight();
			},

			changeItem : function( model ) {
				var item_id      = this.getItemID( model ),
					$item        = $( item_id ),
					active_model = model.collection.findWhere({ current_active_history : true }),
					active_index = model.collection.indexOf( active_model ),
					item_index   = model.collection.indexOf( model );

				// Setting the passed model as class' options so the template can be rendered correctly
				this.options = model;

				// Remove all class related to changed item
				this.$el.find('li').removeClass( 'undo redo active' );

				// Update currently item class, relative to current index
				// Use class change instead of redraw the whole index using template() because verb+noun changing is too tricky
				if ( active_index === item_index ) {
					$item.addClass( 'active' );

					this.$el.find('li:lt('+ $item.index() +')').addClass( 'redo' );

					this.$el.find('li:gt('+ $item.index() +')').addClass( 'undo' );
				} else {
					// Change upon history is tricky because there is no active model found. Assume that everything is undo action
					this.$el.find('li:not( .active, .redo )').addClass( 'undo' );
				}

				// Fix max-height for history visualizer
				this.setHistoriesHeight();
			},

			removeItem : function( model ) {
				var item_id = this.getItemID( model );

				// Remove model's item from UI
				this.$el.find( item_id ).remove();

				// Fix max-height for history visualizer
				this.setHistoriesHeight();
			},

			setHistoryMeta : function( verb, noun, noun_alias, addition ) {
				if ( ! _.isUndefined( verb ) ) {
					this.verb = verb;
				}

				if ( ! _.isUndefined( noun ) ) {
					this.noun = noun;
				}

				if ( ! _.isUndefined( noun_alias ) ) {
					this.noun_alias = noun_alias;
				} else {
					this.noun_alias = undefined;
				}

				if ( ! _.isUndefined( addition ) ) {
					this.addition = addition;
				}
			},

			setHistoriesHeight : function() {
				var this_el = this;

				// Wait for 200 ms before making change to ensure that $layout has been changed
				setTimeout( function(){
					var $layout                = $( '#et_pb_layout' ),
						$layout_header         = $layout.find( '.hndle' ),
						$layout_controls       = $( '#et_pb_layout_controls' ),
						visualizer_height      = $layout.outerHeight() - $layout_header.outerHeight() - $layout_controls.outerHeight();

					this_el.$el.css({ 'max-height' : visualizer_height });
				}, 200 );
			},

			rollback : function( event ) {
				event.preventDefault();

				var this_el     = this,
					$clicked_el = $( event.target ),
					$this_el    = $clicked_el.is( 'li' ) ? $clicked_el : $clicked_el.parent('li'),
					timestamp   = $this_el.data( 'timestamp' ),
					model       = this.options.collection.findWhere({ timestamp : timestamp }),
					shortcode   = model.get( 'shortcode' );

				// Turn off other current_active_history
				ET_PageBuilder_App.resetCurrentActiveHistoryMarker();

				// Update undo model's current_active_history
				model.set( { current_active_history : true });

				// add loading state
				ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

				// Set shortcode to editor
				et_pb_set_content( 'content', shortcode, 'saving_to_content' );

				// Rebuild the builder
				setTimeout( function(){
					var $builder_container = $( '#et_pb_layout' ),
						builder_height     = $builder_container.innerHeight();

					$builder_container.css( { 'height' : builder_height } );

					ET_PageBuilder_App.removeAllSections();

					ET_PageBuilder_App.$el.find( '.et_pb_section' ).remove();

					// Ensure that no history is added for rollback
					ET_PageBuilder_App.enable_history = false;

					ET_PageBuilder_App.createLayoutFromContent( et_prepare_template_content( shortcode ), '', '', { is_reinit : 'reinit' } );

					// Auto turn on Split Testing if the history has Split testing data
					if ( ET_PageBuilder_AB_Testing.is_active_based_on_models() ) {
						ET_PageBuilder_AB_Testing.toggle_status( true );

						et_reinitialize_builder_layout();
					} else {
						ET_PageBuilder_AB_Testing.toggle_status( false );
					}

					$builder_container.css( { 'height' : 'auto' } );

					// remove loading state
					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

					// Update undo button state
					ET_PageBuilder_App.updateHistoriesButtonState();
				}, 600 );
			}
		} );

		ET_PageBuilder.AppView = window.wp.Backbone.View.extend( {

			el : $('#et_pb_main_container'),

			template : _.template( $('#et-builder-app-template').html() ),

			template_button : _.template( $('#et-builder-add-specialty-section-button').html() ),

			events: {
				'click .et-pb-layout-buttons-save' : 'saveLayout',
				'click .et-pb-layout-buttons-load' : 'loadLayout',
				'click .et-pb-layout-buttons-clear' : 'clearLayout',
				'click .et-pb-layout-buttons-history' : 'toggleHistory',
				'click #et-pb-histories-visualizer-overlay' : 'closeHistory',
				'contextmenu #et-pb-histories-visualizer-overlay' : 'closeHistory',
				'click .et-pb-layout-buttons-redo' : 'redo',
				'click .et-pb-layout-buttons-undo' : 'undo',
				'click .et-pb-layout-buttons-view-ab-stats' : 'viewABStats',
				'click .et-pb-layout-buttons-settings' : 'settings',
				'contextmenu .et-pb-layout-buttons-save' : 'showRightClickOptions',
				'contextmenu .et-pb-layout-buttons-load' : 'showRightClickOptions',
				'contextmenu .et-pb-layout-buttons-clear' : 'showRightClickOptions',
				'contextmenu .et-pb-layout-buttons-redo' : 'showRightClickOptions',
				'contextmenu .et-pb-layout-buttons-undo' : 'showRightClickOptions',
				'contextmenu #et_pb_main_container_right_click_overlay' : 'showRightClickOptions',
				'click #et_pb_main_container_right_click_overlay' : 'hideRightClickOptions'
			},

			initialize : function() {
				this.listenTo( this.collection, 'add', this.addModule );
				this.listenTo( ET_PageBuilder_Histories, 'add', this.addVisualizeHistoryItem );
				this.listenTo( ET_PageBuilder_Histories, 'change', this.changeVisualizeHistoryItem );
				this.listenTo( ET_PageBuilder_Histories, 'remove', this.removeVisualizeHistoryItem );
				this.listenTo( ET_PageBuilder_Events, 'et-sortable:update', _.debounce( this.saveAsShortcode, 128 ) );
				this.listenTo( ET_PageBuilder_Events, 'et-model-changed-position-within-column', _.debounce( this.saveAsShortcode, 128 ) );
				this.listenTo( ET_PageBuilder_Events, 'et-module:removed', _.debounce( this.saveAsShortcode, 128 ) );
				this.listenTo( ET_PageBuilder_Events, 'et-pb-loading:started', this.startLoadingAnimation );
				this.listenTo( ET_PageBuilder_Events, 'et-pb-loading:ended', this.endLoadingAnimation );
				this.listenTo( ET_PageBuilder_Events, 'et-pb-content-updated', this.recalculateModulesOrder );
				this.listenTo( ET_PageBuilder_Events, 'et-advanced-module:updated_order', this.updateAdvancedModulesOrder );
				this.listenTo( ET_PageBuilder_Events, 'et-pb-content-updated', this.updateYoastContent );

				this.$builder_toggle_button = $( 'body' ).find( '#et_pb_toggle_builder' );
				this.$builder_toggle_button_wrapper = $( 'body' ).find( '.et_pb_toggle_builder_wrapper' );

				this.render();

				this.maybeGenerateInitialLayout();

				// Set current builder product name and version as the last version used to save this post/page.
				$( '#et_builder_version' ).val( 'BB|' + window.et_builder_product_name + '|' + window.et_builder_version );
			},

			render : function() {
				this.$el.html( this.template() );

				this.makeSectionsSortable();

				this.addLoadingAnimation();

				$( '#et_pb_main_container_right_click_overlay' ).remove();

				this.$el.prepend('<div id="et_pb_main_container_right_click_overlay" />');

				this.updateHistoriesButtonState();

				return this;
			},

			addLoadingAnimation : function() {
				$( 'body' ).append( '<div id="et_pb_loading_animation"></div>' );

				this.$loading_animation = $( '#et_pb_loading_animation' ).hide();
			},

			startLoadingAnimation : function() {
				if ( this.pageBuilderIsActive() ) {
					// place the loading animation container before the closing body tag
					if ( this.$loading_animation.next().length ) {
						$( 'body' ).append( this.$loading_animation );
						this.$loading_animation = $( '#et_pb_loading_animation' );
					}

					this.$loading_animation.show();

					this.isLoading = true;
				};
			},

			endLoadingAnimation : function() {
				this.$loading_animation.hide();

				this.isLoading = false;
			},

			pageBuilderIsActive : function() {
				// check the button wrapper class as well because button may not be added in some cases
				return this.$builder_toggle_button.hasClass( 'et_pb_builder_is_used' ) || this.$builder_toggle_button_wrapper.hasClass( 'et_pb_builder_is_used' );
			},

			saveLayout : function( event ) {
				event.preventDefault();

				if ( ET_PageBuilder_AB_Testing.is_active() ) {
					ET_PageBuilder_AB_Testing.alert( 'cannot_save_app_layout_has_ab_testing' );
					return;
				}

				et_pb_close_all_right_click_options();

				et_pb_create_prompt_modal( 'save_layout' );
			},

			loadLayout : function( event ) {
				event.preventDefault();

				var view;

				et_pb_close_all_right_click_options();

				if ( ET_PageBuilder_AB_Testing.is_active() ) {
					ET_PageBuilder_AB_Testing.alert( 'cannot_load_layout_has_ab_testing' );
					return;
				}

				view = new ET_PageBuilder.ModalView( {
					attributes : {
						'data-open_view' : 'save_layout'
					},
					view : this
				} );

				$('body').append( view.render().el );
			},

			clearLayout : function( event ) {
				event.preventDefault();

				et_pb_close_all_right_click_options();

				if ( ET_PageBuilder_AB_Testing.is_active() ) {
					ET_PageBuilder_AB_Testing.alert( 'cannot_clear_layout_has_ab_testing' );
					return;
				}

				et_pb_create_prompt_modal( 'clear_layout' );
			},

			getHistoriesCount : function() {
				return this.options.history.length;
			},

			getHistoriesIndex : function() {
				var active_model       = this.options.history.findWhere({ current_active_history : true }),
					active_model_index = _.isUndefined( active_model ) ? ( this.options.history.models.length - 1 ) : this.options.history.indexOf( active_model );

				return active_model_index;
			},

			isDoingCombination : function() {
				if ( _.isUndefined( this.is_doing_combination ) ) {
					return false;
				} else {
					return this.is_doing_combination;
				}
			},

			enableHistory : function() {
				if ( _.isUndefined( this.enable_history ) ) {
					return false;
				} else {
					return this.enable_history;
				}
			},

			allowHistorySaving : function( verb, noun, noun_alias, addition ) {
				this.enable_history = true;

				// Enable history saving and set meta for history
				ET_PageBuilder_Visualize_Histories.setHistoryMeta( verb, noun, noun_alias, addition );
			},

			reviseHistories : function() {
				var model,
					this_el = this;

				if ( this.hasRedo() ) {
					// Prepare reversed index (deleting unused model using ascending index changes the order of collection)
					var history_index = _.range( ( this.getHistoriesIndex() + 1 ), this.getHistoriesCount() ).reverse();

					// Loop the reversed index then delete the matched models
					_.each( history_index, function( index ) {
						model = this_el.options.history.at( index );
						this_el.options.history.remove( model );
					} );
				}

				// Update undo button state
				this.updateHistoriesButtonState();
			},

			resetCurrentActiveHistoryMarker : function() {
				var current_active_histories = this.options.history.where({ current_active_history : true });

				if ( ! _.isEmpty( current_active_histories ) ) {
					_.each( current_active_histories, function( current_active_history ) {
						current_active_history.set({ current_active_history : false });
					} );
				}

			},

			hasUndo : function() {
				return this.getHistoriesIndex() > 0 ? true : false;
			},

			hasRedo : function() {
				return ( this.getHistoriesCount() - this.getHistoriesIndex() ) > 1 ? true : false;
			},

			hasOverlayRendered : function() {
				if ( $('.et_pb_modal_overlay').length ) {
					return true;
				}

				return false;
			},

			updateHistoriesButtonState : function() {
				if ( this.hasUndo() ) {
					$( '.et-pb-layout-buttons-undo' ).removeClass( 'disabled' );
				} else {
					$( '.et-pb-layout-buttons-undo' ).addClass( 'disabled' );
				}

				if ( this.hasRedo() ) {
					$( '.et-pb-layout-buttons-redo' ).removeClass( 'disabled' );
				} else {
					$( '.et-pb-layout-buttons-redo' ).addClass( 'disabled' );
				}

				if ( this.hasUndo() || this.hasRedo() ) {
					$( '.et-pb-layout-buttons-history' ).removeClass( 'disabled' );
				} else {
					$( '.et-pb-layout-buttons-history' ).addClass( 'disabled' );
				}
			},

			getUndoModel : function() {
				var model = this.options.history.at( this.getHistoriesIndex() - 1 );

				if ( _.isUndefined( model ) ) {
					return false;
				} else {
					return model;
				}
			},

			undo : function( event ) {
				event.preventDefault();

				var this_el = this,
					undo_model = this.getUndoModel(),
					undo_content,
					current_active_histories;

				// Bail if there's no undo histories to be used
				if ( ! this.hasUndo() ) {
					return;
				}

				// Bail if no undo model found
				if ( _.isUndefined( undo_model ) ) {
					return;
				}

				// Bail if there is overlay rendered (usually via hotkeys)
				if ( this.hasOverlayRendered() ) {
					return;
				}

				// Get undo content
				undo_content     = undo_model.get( 'shortcode' );

				// Turn off other current_active_history
				this.resetCurrentActiveHistoryMarker();

				// Update undo model's current_active_history
				undo_model.set( { current_active_history : true });

				// add loading state
				ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

				// Set last history's content into main editor
				et_pb_set_content( 'content', undo_content, 'saving_to_content' );

				// Rebuild the builder
				setTimeout( function(){
					var $builder_container = $( '#et_pb_layout' ),
						builder_height     = $builder_container.innerHeight();

					$builder_container.css( { 'height' : builder_height } );

					ET_PageBuilder_App.removeAllSections();

					ET_PageBuilder_App.$el.find( '.et_pb_section' ).remove();


					// Temporarily disable history until new layout has been generated
					this_el.enable_history = false;

					ET_PageBuilder_App.createLayoutFromContent( et_prepare_template_content( undo_content ), '', '', { is_reinit : 'reinit' } );

					$builder_container.css( { 'height' : 'auto' } );

					// remove loading state
					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

					// Update undo button state
					this_el.updateHistoriesButtonState();
				}, 600 );
			},

			viewABStats : function( event ) {
				event.preventDefault();

				// View Split Testing stats is disabled on Divi Library
				if ( et_pb_options.is_divi_library === "1" ) {
					return;
				}

				et_pb_create_prompt_modal( 'view_ab_stats' );
			},

			settings : function( event ) {
				event.preventDefault();

				// Builder settings is disabled on Divi Library
				if ( et_pb_options.is_divi_library === "1" ) {
					return;
				}

				et_pb_create_prompt_modal( 'open_settings' );
			},

			getRedoModel : function() {
				var model = this.options.history.at( this.getHistoriesIndex() + 1 );

				if ( _.isUndefined( model ) ) {
					return false;
				} else {
					return model;
				}
			},

			toggleHistory : function( event ) {
				event.preventDefault();

				var $et_pb_history_visualizer = $('#et-pb-histories-visualizer');

				if ( $et_pb_history_visualizer.hasClass( 'active' ) ) {
					$et_pb_history_visualizer.addClass( 'fadeout' );

					// Remove class after being animated
					setTimeout( function() {
						$et_pb_history_visualizer.removeClass( 'fadeout' );
					}, 500 );
				}

				$( '.et-pb-layout-buttons-history, #et-pb-histories-visualizer, #et-pb-histories-visualizer-overlay' ).toggleClass( 'active' );
			},

			closeHistory : function( event ) {
				event.preventDefault();

				this.toggleHistory( event );
			},

			redo : function( event ) {
				event.preventDefault();

				var this_el = this,
					redo_model = this.getRedoModel(),
					redo_model_index,
					redo_content,
					current_active_histories;

				// Bail if there's no redo histories to be used
				if ( ! this.hasRedo() ) {
					return;
				}

				// Bail if no redo model found
				if ( _.isUndefined( redo_model ) || ! redo_model ) {
					return;
				}

				// Bail if there is overlay rendered (usually via hotkeys)
				if ( this.hasOverlayRendered() ) {
					return;
				}

				redo_model_index = this.options.history.indexOf( redo_model );
				redo_content     = redo_model.get( 'shortcode' );

				// Turn off other current_active_history
				this.resetCurrentActiveHistoryMarker();

				// Update redo model's current_active_history
				redo_model.set( { current_active_history : true });

				// add loading state
				ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

				// Set last history's content into main editor
				et_pb_set_content( 'content', redo_content, 'saving_to_content' );

				// Rebuild the builder
				setTimeout( function(){
					var $builder_container = $( '#et_pb_layout' ),
						builder_height     = $builder_container.innerHeight();

					$builder_container.css( { 'height' : builder_height } );

					ET_PageBuilder_App.removeAllSections();

					ET_PageBuilder_App.$el.find( '.et_pb_section' ).remove();

					// Temporarily disable history until new layout has been generated
					this_el.enable_history = false;

					ET_PageBuilder_App.createLayoutFromContent( et_prepare_template_content( redo_content ), '', '', { is_reinit : 'reinit' } );

					$builder_container.css( { 'height' : 'auto' } );

					// remove loading state
					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

					// Update redo button state
					this_el.updateHistoriesButtonState();
				}, 600 );
			},

			addHistory : function( content ) {
				if ( this.enableHistory() && ! this.isDoingCombination() ) {
					var date = new Date(),
						hour = date.getHours() > 12 ? date.getHours() - 12 : date.getHours(),
						minute = date.getMinutes(),
						datetime_suffix = date.getHours() > 12 ? "PM" : "AM";

					// If there's a redo, remove models after active model
					if ( this.hasRedo() ) {
						this.reviseHistories();
					}

					this.resetCurrentActiveHistoryMarker();

					// Save content to builder history for undo/redo
					this.options.history.add({
						timestamp : _.now(),
						datetime : ( "0" + hour).slice(-2) + ":" + ( "0" + minute ).slice(-2) + " " + datetime_suffix,
						shortcode : content,
						current_active_history : true,
						verb : ET_PageBuilder_Visualize_Histories.verb,
						noun : ET_PageBuilder_Visualize_Histories.noun
					}, { validate : true });

					// Return history meta to default. Prevent confusion and for debugging
					ET_PageBuilder_Visualize_Histories.setHistoryMeta( 'did', 'something' );
				}

				// Update undo button state
				this.updateHistoriesButtonState();
			},

			addVisualizeHistoryItem : function( model ) {
				ET_PageBuilder_Visualize_Histories.addItem( model );
			},

			changeVisualizeHistoryItem : function( model ) {
				ET_PageBuilder_Visualize_Histories.changeItem( model );
			},

			removeVisualizeHistoryItem : function( model ) {
				ET_PageBuilder_Visualize_Histories.removeItem( model );
			},

			maybeGenerateInitialLayout : function() {
				var module_id = ET_PageBuilder_Layout.generateNewId(),
					this_el = this;

				ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

				setTimeout( function() {
					var fix_shortcodes = true,
						content = '';

					// check whether the tinyMCE container is loaded already if not - try again.
					if ( typeof window.tinyMCE !== 'undefined' && window.tinyMCE.get( 'content' ) && ! window.tinyMCE.get( 'content' ).isHidden() && ! $( 'iframe#content_ifr' ).length ) {
						et_pb_bulder_loading_attempts++;

						//show failure modal after 30 unsuccessful attempts
						if ( 30 < et_pb_bulder_loading_attempts ) {
							var $failure_notice_template = $( '#et-builder-failure-notice-template' );

							ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

							$( '#et_pb_main_container' ).removeClass( 'et_pb_loading_animation' );

							$( 'body' ).addClass( 'et_pb_stop_scroll' ).append( $failure_notice_template.html() );

							return;
						}

						this_el.maybeGenerateInitialLayout();
						return;
					}

					/*
					 * Visual editor adds paragraph tags around shortcodes,
					 * it causes &nbsp; to be inserted into a module content area
					 */
					content = et_pb_get_content( 'content', fix_shortcodes );

					// Enable history saving and set meta for history
					if ( content !== '' ) {
						this_el.allowHistorySaving( 'loaded', 'page' );
					}

					// Save page loaded
					this_el.addHistory( content );

					if  ( this_el.pageBuilderIsActive() ) {
						if ( -1 === content.indexOf( '[et_pb_') ) {
							ET_PageBuilder_App.reInitialize();
						} else if ( -1 !== content.indexOf( 'specialty_placeholder') ) {
							this_el.createLayoutFromContent( et_prepare_template_content( content ) );
							$( '.et_pb_section_specialty' ).append( this_el.template_button() );
						} else {
							this_el.createLayoutFromContent( et_prepare_template_content( content ) );
						}
					} else {
						this_el.createLayoutFromContent( content );
					}

					et_pb_maybe_apply_wpautop_to_models( et_get_editor_mode(), 'initial_load' );

					ET_PageBuilder_Events.trigger( 'et-pb-content-updated' );

					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

					$( '#et_pb_main_container' ).addClass( 'et_pb_loading_animation' );

					setTimeout( function() {
						$( '#et_pb_main_container' ).removeClass( 'et_pb_loading_animation' );
					}, 500 );

					// start listening to any collection events after all modules have been generated
					this_el.listenTo( this_el.collection, 'change reset add', _.debounce( this_el.saveAsShortcode, 128 ) );

					ET_PageBuilder_AB_Testing.update();
				}, 500 );
			},

			wp_regexp_not_global : _.memoize( function( tag ) {
				return new RegExp( '\\[(\\[?)(' + tag + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)' );
			}),

			getShortCodeParentTags : function () {
				var shortcodes = 'et_pb_section|et_pb_row|et_pb_column|et_pb_column_inner|et_pb_row_inner'.split('|');

				shortcodes = shortcodes.concat( et_pb_options.et_builder_module_parent_shortcodes.split('|') );
				shortcodes = shortcodes.join('|');
				return shortcodes;
			},

			getShortCodeChildTags : function () {
				return et_pb_options.et_builder_module_child_shortcodes;
			},

			getShortCodeRawContentTags : function () {
				var raw_content_shortcodes = et_pb_options.et_builder_module_raw_content_shortcodes,
					raw_content_shortcodes_array;

				raw_content_shortcodes_array = raw_content_shortcodes.split( '|' );

				return raw_content_shortcodes_array;
			},
			//ignore_template_tag, current_row_cid, global_id, is_reinit, after_section, global_parent
			createLayoutFromContent : function( content, parent_cid, inner_shortcodes, additional_options, parent_address ) {
				var this_el = this,
					et_pb_shortcodes_tags = typeof inner_shortcodes === 'undefined' || '' === inner_shortcodes ? this.getShortCodeParentTags() : this.getShortCodeChildTags(),
					reg_exp = window.wp.shortcode.regexp( et_pb_shortcodes_tags ),
					inner_reg_exp = this.wp_regexp_not_global( et_pb_shortcodes_tags ),
					matches = content.match( reg_exp ),
					et_pb_raw_shortcodes = this.getShortCodeRawContentTags(),
					additional_options_received = typeof additional_options === 'undefined' ? {} : additional_options;

				var migrations    = _.isUndefined( et_pb_options.et_pb_module_settings_migrations ) ? false : et_pb_options.et_pb_module_settings_migrations;
				var name_changes  = _.isUndefined( migrations.name_changes ) ? false : migrations.name_changes;
				var value_changes = _.isUndefined( migrations.value_changes ) ? false : migrations.value_changes;

				_.each( matches, function ( shortcode, index ) {
					var shortcode_element = shortcode.match( inner_reg_exp ),
						shortcode_name = shortcode_element[2],
						shortcode_attributes = shortcode_element[3] !== ''
							? window.wp.shortcode.attrs( shortcode_element[3] )
							: '',
						shortcode_content = shortcode_element[5],
						module_cid = ET_PageBuilder_Layout.generateNewId(),
						module_settings,
						prefixed_attributes = {},
						found_inner_shortcodes = typeof shortcode_content !== 'undefined' && shortcode_content !== '' && shortcode_content.match( reg_exp ),
						global_module_id = '',
						is_structure_element = $.inArray( shortcode_name, [ 'et_pb_section', 'et_pb_row', 'et_pb_column', 'et_pb_row_inner', 'et_pb_column_inner' ] ) > -1 ;

					if ( is_structure_element ) {
						shortcode_name = shortcode_name.replace( 'et_pb_', '' );
					}

					module_settings = {
						type : shortcode_name,
						cid : module_cid,
						created : 'manually',
						module_type : shortcode_name
					};

					if ( typeof additional_options_received.current_row_cid !== 'undefined' && '' !== additional_options_received.current_row_cid ) {
						module_settings['current_row'] = additional_options_received.current_row_cid;
					}

					if ( typeof additional_options_received.global_parent !== 'undefined' && '' !== additional_options_received.global_parent ) {
						module_settings['et_pb_global_parent'] = additional_options_received.global_parent;
						module_settings['global_parent_cid'] = additional_options_received.global_parent_cid;
					}

					if ( shortcode_name === 'section' && ( typeof additional_options_received.after_section !== 'undefined' && '' !== additional_options_received.after_section ) ) {
						module_settings['after_section'] = additional_options_received.after_section;
					}

					if ( shortcode_name !== 'section' ) {
						module_settings['parent'] = parent_cid;
					}

					if ( shortcode_name.indexOf( 'et_pb_' ) !== -1 ) {
						module_settings['type'] = 'module';
					}

					// generate default admin_label
					module_settings['admin_label'] = ET_PageBuilder_Layout.getDefaultAdminLabel( shortcode_name );

					module_settings._address = index.toString();

					if ( ! _.isUndefined( parent_address ) ) {
						module_settings._address = parent_address + '.' + module_settings._address;
					}

					if ( _.isObject( shortcode_attributes['named'] ) ) {
						var fill_legacy_global_options = false;

						// prepare global module selective sync attributes
						if ( 'global' === et_pb_options.is_global_template && ! is_structure_element && typeof shortcode_attributes['named']['template_type'] !== 'undefined' && 'module' === shortcode_attributes['named']['template_type'] ) {
							if ( 'updated' === et_pb_options.selective_sync_status ) {
								et_pb_all_unsynced_options[ et_pb_options.template_post_id ] = et_pb_options.excluded_global_options;
							} else {
								fill_legacy_global_options = true;
								et_pb_all_legacy_synced_options[ et_pb_options.template_post_id ] = [];
							}
						}

						global_module_id = typeof shortcode_attributes['named']['global_module'] !== 'undefined' && '' === global_module_id ? shortcode_attributes['named']['global_module'] : global_module_id;

						// BEGIN Settings Migrations
						if ( name_changes &&  ! _.isUndefined( name_changes[shortcode_name] ) ) {
							_.forEach( name_changes[shortcode_name], function( new_name, old_name ) {
								if ( ! _.isUndefined( shortcode_attributes['named'][old_name] ) && _.isUndefined( shortcode_attributes['named'][new_name] ) ) {
									shortcode_attributes['named'][new_name] = shortcode_attributes['named'][old_name];
								}
							} );
						}

						if ( value_changes &&  ! _.isUndefined( value_changes[module_settings._address] ) ) {
							_.forEach( value_changes[module_settings._address], function( new_value, setting_name ) {
								shortcode_attributes['named'][setting_name] = new_value;
							} );
						}
						// END Settings Migrations

						for ( var key in shortcode_attributes['named'] ) {
							if ( typeof additional_options_received.ignore_template_tag === 'undefined' || '' === additional_options_received.ignore_template_tag || ( 'ignore_template' === additional_options_received.ignore_template_tag && 'template_type' !== key ) ) {
								var prefixed_key = key !== 'admin_label' && key !== 'specialty_columns' ? 'et_pb_' + key : key;

								// fill the array of legacy global synced options for module
								if ( fill_legacy_global_options ) {
									et_pb_all_legacy_synced_options[ et_pb_options.template_post_id ].push( key );
								}

								if ( ( shortcode_name === 'column' || shortcode_name === 'column_inner' ) && prefixed_key === 'et_pb_type' )
									prefixed_key = 'layout';

								prefixed_attributes[prefixed_key] = shortcode_attributes['named'][key];
							}
						}

						module_settings = _.extend( module_settings, prefixed_attributes );

					}

					if ( typeof module_settings['specialty_columns'] !== 'undefined' ) {
						module_settings['layout_specialty'] = '1';
						module_settings['specialty_columns'] = parseInt( module_settings['specialty_columns'] );
					}

					if ( ! found_inner_shortcodes ) {
						if ( $.inArray( shortcode_name, et_pb_raw_shortcodes ) > -1 ) {
							module_settings['et_pb_raw_content'] = _.unescape( shortcode_content );
							// replace line-break placeholders with real line-breaks
							module_settings['et_pb_raw_content'] = module_settings['et_pb_raw_content'].replace( /<!-- \[et_pb_line_break_holder\] -->/g, '\n' );
						} else {
							module_settings['et_pb_content_new'] = shortcode_content;
						}
					}

					// convert line break placeholders into real line-breaks for the message pattern option in Contact Form module
					if ( 'et_pb_contact_form' === shortcode_name && typeof module_settings['et_pb_custom_message'] !== 'undefined' ) {
						module_settings['et_pb_custom_message'] = module_settings['et_pb_custom_message'].replace( /\|\|et_pb_line_break_holder\|\|/g, '\r\n' );
					}

					if ( ! module_settings['et_pb_disabled'] !== 'undefined' && module_settings['et_pb_disabled'] === 'on' ) {
						module_settings.className = ' et_pb_disabled';
					}

					if ( ! module_settings['et_pb_locked'] !== 'undefined' && module_settings['et_pb_locked'] === 'on' ) {
						module_settings.className = ' et_pb_locked';
					}

					if ( typeof additional_options_received.global_id !== 'undefined' && '' !== additional_options_received.global_id ) {
						module_settings['et_pb_global_module'] = additional_options_received.global_id;
					}

					this_el.collection.add( [ module_settings ] );

					if ( 'reinit' === additional_options_received.is_reinit || ( global_module_id === '' || ( global_module_id !== '' && 'row' !== shortcode_name && 'row_inner' !== shortcode_name && 'section' !== shortcode_name ) ) ) {
						if ( found_inner_shortcodes ) {
							var global_parent_id = typeof additional_options_received.global_parent === 'undefined' || '' === additional_options_received.global_parent ? global_module_id : additional_options_received.global_parent,
								global_parent_cid_new = typeof additional_options_received.global_parent_cid === 'undefined' || '' === additional_options_received.global_parent_cid
									? typeof global_module_id !== 'undefined' && '' !== global_module_id ? module_cid : ''
									: additional_options_received.global_parent_cid;

							this_el.createLayoutFromContent( shortcode_content, module_cid, '', { is_reinit : additional_options_received.is_reinit, global_parent : global_parent_id, global_parent_cid : global_parent_cid_new }, module_settings._address );
						}
					} else {
						//calculate how many global modules we requested on page
						et_pb_globals_requested++;

						et_pb_load_global_row( global_module_id, module_cid, shortcode );
						this_el.createLayoutFromContent( shortcode_content, module_cid, '', { is_reinit : 'reinit' }, module_settings._address );
					}
				} );
			},

			addModule : function( module ) {
				var view,
					modal_view,
					row_parent_view,
					row_layout,
					view_settings = {
						model : module,
						collection : ET_PageBuilder_Modules
					},
					cloned_cid = typeof module.get('cloned_cid') !== 'undefined' ? module.get('cloned_cid') : false;

				switch ( module.get( 'type' ) ) {
					case 'section' :
						view = new ET_PageBuilder.SectionView( view_settings );

						ET_PageBuilder_Layout.addView( module.get('cid'), view );

						if ( ! _.isUndefined( module.get( 'view' ) ) ){
							module.get( 'view' ).$el.after( view.render().el );
						} else if ( typeof module.get( 'after_section' ) !== 'undefined' && '' !== module.get( 'after_section' ) ) {
							ET_PageBuilder_Layout.getView( module.get( 'after_section' ) ).$el.after( view.render().el );
						} else if ( typeof module.get( 'current_row' ) !== 'undefined' ) {
							this.replaceElement( module.get( 'current_row' ), view );
						} else if ( cloned_cid ) {
							this.$el.find( 'div[data-cid="' + cloned_cid + '"]' ).closest('.et_pb_section').after( view.render().el );
						} else {
							this.$el.append( view.render().el );
						}

						if ( 'on' === module.get( 'et_pb_fullwidth' ) ) {
							$( view.render().el ).addClass( 'et_pb_section_fullwidth' );

							var sub_view = new ET_PageBuilder.ColumnView( view_settings );

							view.addChildView( sub_view );

							$( view.render().el ).find( '.et-pb-section-content' ).append( sub_view.render().el );
						}

						if ( 'on' === module.get( 'et_pb_specialty' ) && 'auto' === module.get( 'created' ) && ! module.get( 'pasted_module' ) ) {
							$( view.render().el ).addClass( 'et_pb_section_specialty' );

							var et_view;

							et_view = new ET_PageBuilder.ModalView( {
								model : view_settings.model,
								collection : view_settings.collection,
								attributes : {
									'data-open_view' : 'column_specialty_settings'
								},
								et_view : view,
								view : view
							} );

							$('body').append( et_view.render().el );
						}

						// add Rows layout once the section has been created in "auto" mode
						// skip this step if pasting section.
						if ( ! module.get( 'pasted_module' ) && 'manually' !== module.get( 'created' ) && 'on' !== module.get( 'et_pb_fullwidth' ) && 'on' !== module.get( 'et_pb_specialty' ) ) {
							view.addRow();
						}

						break;
					case 'row' :
					case 'row_inner' :
						view = new ET_PageBuilder.RowView( view_settings );

						if ( module.get( 'parent' ) !== '' ) {
							ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.et-pb-insert-row' ).hide();
						}

						ET_PageBuilder_Layout.addView( module.get('cid'), view );

						/*this.$("[data-cid=" + module.get('parent') + "]").append( view.render().el );*/
						if ( ! _.isUndefined( module.get( 'current_row' ) ) ) {
							this.replaceElement( module.get( 'current_row' ), view );
						} else if ( ! _.isUndefined( module.get( 'appendAfter' ) ) ) {
							module.get( 'appendAfter' ).after( view.render().el );
						} else if ( cloned_cid ) {
							ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( 'div[data-cid="' + cloned_cid + '"]' ).parent().after( view.render().el );
						} else {
							if ( ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.et-pb-section-content' ).length ) {
								ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.et-pb-section-content' ).append( view.render().el );
							} else {
								ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '> .et-pb-insert-module, > .et-pb-insert-row' ).hide().end().append( view.render().el );
							}
						}

						// unset the columns_layout so it'll be calculated properly when columns added
						module.unset( 'columns_layout' );

						// add parent view to inner rows that have been converted from shortcodes
						if ( module.get('created') === 'manually' && module.get('module_type') === 'row_inner' ) {
							module.set( 'view', ET_PageBuilder_Layout.getView( module.get( 'parent' ) ), { silent : true } );
						}

						/*module.get( 'view' ).$el.find( '.et-pb-section-content' ).append( view.render().el );*/

						break;
					case 'column' :
					case 'column_inner' :
						view_settings['className'] = 'et-pb-column et-pb-column-' + module.get( 'layout' );

						if ( ! _.isUndefined( module.get( 'layout_specialty' ) ) && '1' === module.get( 'layout_specialty' ) ) {
							view_settings['className'] += ' et-pb-column-specialty';
						}

						view = new ET_PageBuilder.ColumnView( view_settings );

						ET_PageBuilder_Layout.addView( module.get('cid'), view );

						if ( _.isUndefined( module.get( 'layout_specialty' ) ) ) {
							/* Need to pass the columns layout into the parent row model to save the row template properly */
							row_parent_view = ET_PageBuilder_Layout.getView( module.get( 'parent' ) );
							row_layout = typeof row_parent_view.model.get( 'columns_layout' ) !== 'undefined' ? row_parent_view.model.get( 'columns_layout' ) + ',' + module.get( 'layout' ) : module.get( 'layout' );
							row_parent_view.model.set( 'columns_layout', row_layout );

							if ( ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.get( 'et_pb_specialty' ) !== 'on' ) {
								ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.et-pb-row-container' ).append( view.render().el );

								ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).toggleInsertColumnButton();
							} else {
								ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.et-pb-section-content' ).append( view.render().el );
							}
						} else {
							ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.et-pb-section-content' ).append( view.render().el );

							if ( '1' === module.get( 'layout_specialty' ) ) {
								if ( 'manually' !== module.get( 'created' ) ) {
									this.collection.add( [ {
										type : 'row',
										module_type : 'row',
										cid : ET_PageBuilder_Layout.generateNewId(),
										parent : module.get( 'cid' ),
										view : view,
										admin_label : et_pb_options.noun['row']
									} ] );
								}

								ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.set( 'specialty_columns', parseInt( module.get( 'specialty_columns' ) ) );
							}
						}

						/*module.get( 'view' ).$el.find( '.et-pb-row-container' ).append( view.render().el );*/

						/*this.$("[data-cid=" + module.get('parent') + "] .et-pb-row-container").append( view.render().el );*/

						break;
					case 'module' :
						view_settings['attributes'] = {
							'data-cid' : module.get( 'cid' )
						}

						if ( module.get( 'mode' ) !== 'advanced' && module.get( 'created' ) === 'manually' && ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.get( 'module_type' ) === 'column_inner' ) {
							var inner_column_parent_row = ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.get( 'parent' );

							ET_PageBuilder_Layout.getView( inner_column_parent_row ).$el.find( '.et-pb-insert-column' ).hide();
						}

						if ( typeof module.get( 'mode' ) !== 'undefined' && module.get( 'mode' ) === 'advanced' ) {
							// create sortable tab

							view = new ET_PageBuilder.AdvancedModuleSettingView( view_settings );

							module.attributes.view.child_views.push( view );

							if ( typeof module.get( 'cloned_cid' ) !== 'undefined' && '' !== module.get( 'cloned_cid' ) ) {
								ET_PageBuilder_Layout.getView( module.get( 'cloned_cid' ) ).$el.after( view.render().el );
							} else {
								ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find('.et-pb-sortable-options').append( view.render().el );
							}

							ET_PageBuilder_Layout.addView( module.get('cid'), view );


						} else {
							var template_type = '';

							ET_PageBuilder_Events.trigger( 'et-new_module:show_settings' );

							view = new ET_PageBuilder.BlockModuleView( view_settings );

							if ( typeof module.attributes.view !== 'undefined' && module.attributes.view.model.get( 'et_pb_fullwidth' ) === 'on' ) {
								ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).addChildView( view );
								template_type = ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.get( 'et_pb_template_type' );
							} else if ( typeof module.attributes.view !== 'undefined' ) {
								template_type = ET_PageBuilder_Layout.getView( ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.get( 'parent' ) ).model.get( 'et_pb_template_type' );
							}

							// Append new module in proper position. Clone shouldn't be appended. It should be added after the cloned item
							if ( cloned_cid ) {
								ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( 'div[data-cid="' + cloned_cid + '"]' ).after( view.render().el );
							} else {
								// if .et-pb-insert-module button exists, then add the module before that button. Otherwise append module to the parent
								if ( ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.et-pb-insert-module' ).length ) {
									ET_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.et-pb-insert-module' ).before( view.render().el );
								} else {
									var parent_view = ET_PageBuilder_Layout.getView( module.get( 'parent' ) );

									// append module to appropriate div if it's a fullwidth section
									if ( typeof parent_view.model.get( 'et_pb_fullwidth' ) !== 'undefined' && 'on' === parent_view.model.get( 'et_pb_fullwidth' ) ) {
										parent_view.$el.find( '.et_pb_fullwidth_sortable_area' ).append( view.render().el );
									} else {
										parent_view.$el.append( view.render().el );
									}
								}
							}

							ET_PageBuilder_Layout.addView( module.get('cid'), view );

							if ( typeof template_type !== 'undefined' && 'module' === template_type ) {
								module.set( 'template_type', 'module', { silent : true } );
							}

							if ( 'manually' !== module.get( 'created' ) ) {
								view_settings['attributes'] = {
									'data-open_view' : 'module_settings'
								}
								this.openModuleSettings( view_settings );
							}
						}

						break;
				}

				// Always unset cloned_cid attribute after adding module.
				// It prevents module mishandling for module which is cloned multiple time
				module.unset('cloned_cid');
			},

			openModuleSettings : function( view_settings ) {
				var modal_view = new ET_PageBuilder.ModalView( view_settings ),
					that = this;

				et_modal_view_rendered = modal_view.render();

				if ( false === et_modal_view_rendered ) {
					setTimeout( function() {
						that.openModuleSettings( view_settings );
					}, 500 );

					ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

					return;
				}

				ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

				$('body').append( et_modal_view_rendered.el );
			},

			saveAsShortcode : function( et_model, et_collection, et_options ) {
				var this_el = this,
					action_setting = arguments.length > 0 && typeof arguments[0] === 'object' && arguments[0]['et_action'] || '';

				// set that the bb is in use, which is looked for by fb if the user switches to new tab
				set_this_editor_in_use();

				if ( et_options && et_options['update_shortcodes'] == 'false' ) {
					return;
				}

				shortcode = this_el.generateCompleteShortcode();

				this.addHistory( shortcode );

				ET_PageBuilder_AB_Testing.update();

				setTimeout( function(){
					// Save to content is performed each time, except when a layout is being loaded
					var action = action_setting || '';

					et_pb_set_content( 'content', shortcode, action );

					ET_PageBuilder_Events.trigger( 'et-pb-content-updated' );
				}, 500 );
			},

			generateCompleteShortcode : function( cid, layout_type, ignore_global_tag, ignore_global_tabs, is_saving_global ) {
				var shortcode = '',
					this_el = this,
					all_sections = typeof cid === 'undefined' ? true : false,
					layout_type = typeof layout_type === 'undefined' ? '' : layout_type;

				this.$el.find( '.et_pb_section' ).each( function() {
					var $this_section = $(this).find( '.et-pb-section-content' ),
						include_whole_section = false,
						skip_section = typeof $this_section.data( 'skip' ) === 'undefined' ? false : $this_section.data( 'skip' );

					if ( ( ( false === all_sections && cid === $this_section.data( 'cid' ) ) || true === all_sections ) && true !== skip_section ) {
						shortcode += this_el.generateModuleShortcode( $(this), true, layout_type, ignore_global_tag );
						include_whole_section = true;
					}

					if ( $this_section.closest( '.et_pb_section' ).hasClass( 'et_pb_section_fullwidth' ) ) {
						$this_section.find( '.et_pb_module_block' ).each( function() {
							var fullwidth_module_cid = $( this ).data( 'cid' );
							if ( ( false === all_sections && ( cid === fullwidth_module_cid || true === include_whole_section ) ) || true === all_sections ) {
								shortcode += this_el.generateModuleShortcode( $(this), false, layout_type, ignore_global_tag, '', ignore_global_tabs );
							}
						} );
					} else if ( $this_section.closest( '.et_pb_section' ).hasClass( 'et_pb_section_specialty' ) && ( ( true === include_whole_section || true === all_sections || 'module' === layout_type || 'row' === layout_type ) && true !== skip_section ) ) {
						$this_section.find( '> .et-pb-column' ).each( function() {
							var $this_column = $(this),
								column_cid = $this_column.data( 'cid' ),
								module = ET_PageBuilder_Modules.findWhere( { cid : column_cid } ),
								specialty_columns = module.get( 'layout_specialty' ) === '1' ? ' specialty_columns="' + module.get( 'specialty_columns' ) + '"' : '',
								specialty_column_layout = module.get('layout');

							if ( true === include_whole_section || true === all_sections ) {
								shortcode += '[et_pb_column type="' + specialty_column_layout + '"' + specialty_columns +']';
							}

							if ( $this_column.hasClass( 'et-pb-column-specialty' ) ) {
								// choose each row
								$this_column.find( '.et_pb_row' ).each( function() {
									var $this_row = $(this),
										row_cid = $this_row.find( '.et-pb-row-content' ).data( 'cid' ),
										module = ET_PageBuilder_Modules.findWhere( { cid : row_cid } ),
										include_whole_inner_row = false;

									if ( true === include_whole_section || true === all_sections || ( 'row' === layout_type && row_cid === cid ) ) {
										include_whole_inner_row = true;
										shortcode += this_el.generateModuleShortcode( $(this), true, layout_type, ignore_global_tag, 'row_inner' );
									}

									$this_row.find( '.et-pb-column' ).each( function() {
										var $this_column_inner = $(this),
											column_cid = $this_column_inner.data( 'cid' ),
											module = ET_PageBuilder_Modules.findWhere( { cid : column_cid } );

										if ( true === include_whole_inner_row ) {
											shortcode += '[et_pb_column_inner type="' + module.get('layout') + '" saved_specialty_column_type="' + specialty_column_layout + '"]';
										}

										$this_column_inner.find( '.et_pb_module_block' ).each( function() {
											var inner_module_cid = $( this ).data( 'cid' );

											if ( ( false === all_sections && ( cid === inner_module_cid || true === include_whole_section || true === include_whole_inner_row ) ) || true === all_sections ) {
												shortcode += this_el.generateModuleShortcode( $(this), false, layout_type, ignore_global_tag, '', ignore_global_tabs );
											}
										} );

										if ( true === include_whole_inner_row ) {
											shortcode += '[/et_pb_column_inner]';
										}
									} );

									if ( true === include_whole_section || true === all_sections || ( 'row' === layout_type && row_cid === cid ) ) {
										shortcode += '[/et_pb_row_inner]';
									}
								} );
							} else {
								// choose each module
								$this_column.find( '.et_pb_module_block' ).each( function() {
									var specialty_module_cid = $( this ).data( 'cid' );

									if ( ( false === all_sections && ( cid === specialty_module_cid || true === include_whole_section ) ) || true === all_sections ) {
										shortcode += this_el.generateModuleShortcode( $(this), false, layout_type, ignore_global_tag, '', ignore_global_tabs, is_saving_global );
									}
								} );
							}

							if ( true === include_whole_section || true === all_sections ) {
								shortcode += '[/et_pb_column]';
							}
						} );
					} else {
						$this_section.find( '.et_pb_row' ).each( function() {
							var $this_row = $(this),
								$this_row_content = $this_row.find( '.et-pb-row-content' ),
								row_cid = $this_row_content.data( 'cid' ),
								include_whole_row = false,
								skip_row = typeof $this_row_content.data( 'skip' ) === 'undefined' ? false : $this_row_content.data( 'skip' );

							if ( ( ( false === all_sections && ( cid === row_cid || true === include_whole_section ) ) || true === all_sections ) && true !== skip_row ) {
								shortcode += this_el.generateModuleShortcode( $(this), true, layout_type, ignore_global_tag );
								include_whole_row = true;
							}

							$this_row.find( '.et-pb-column' ).each( function() {
								var $this_column = $(this),
									column_cid = $this_column.data( 'cid' ),
									module = ET_PageBuilder_Modules.findWhere( { cid : column_cid } );

								if ( ( ( false === all_sections && ( true === include_whole_section || true === include_whole_row ) ) || true === all_sections ) && true !== skip_row ) {
									shortcode += '[et_pb_column type="' + module.get('layout') + '"]';
								}

								$this_column.find( '.et_pb_module_block' ).each( function() {
									var module_cid = $( this ).data( 'cid' );
									if ( ( false === all_sections && ( cid === module_cid || true === include_whole_section || true === include_whole_row ) ) || true === all_sections ) {
										shortcode += this_el.generateModuleShortcode( $(this), false, layout_type, ignore_global_tag, '', ignore_global_tabs, is_saving_global );
									}
								} );

								if ( ( ( false === all_sections && ( true === include_whole_section || true === include_whole_row ) ) || true === all_sections ) && true !== skip_row ) {
									shortcode += '[/et_pb_column]';
								}

							} );

							if ( ( ( false === all_sections && ( cid === row_cid || true === include_whole_section ) ) || true === all_sections ) && true !== skip_row ) {
								shortcode += '[/et_pb_row]';
							}

						} );
					}
					if ( ( ( false === all_sections && cid === $this_section.data( 'cid' ) ) || true === all_sections ) && true !== skip_section ) {
						shortcode += '[/et_pb_section]';
					}

				} );

			return shortcode;
			},

			generateModuleShortcode : function( $module, open_tag_only, layout_type, ignore_global_tag, defined_module_type, ignore_global_tabs, is_saving_global ) {
				var attributes = '',
					content = '',
					$this_module = $module,
					is_module_type = ! $this_module.is( '.et_pb_section' ) && ! $this_module.is( '.et_pb_row' ) && ! $this_module.is( '.et_pb_row_inner' ) && ! $this_module.is( '.et_pb_column' ) && ! $this_module.is( '.et_pb_column_inner' ),
					prefix = $this_module.is( '.et_pb_section' ) || $this_module.is( '.et_pb_row' ) || $this_module.is( '.et_pb_row_inner' )
						? 'et_pb_'
						: '',
					module_cid = typeof $this_module.data( 'cid' ) === 'undefined'
						? $this_module.find( '.et-pb-data-cid' ).data( 'cid' )
						: $this_module.data( 'cid' ),
					module = ET_PageBuilder_Modules.find( function( model ) {
						return model.get('cid') == module_cid;
					} ),
					module_type = typeof module !== 'undefined' ? module.get( 'module_type' ) : 'undefined',
					module_settings,
					shortcode,
					template_module_type,
					unsynced_options = [];

				if ( typeof defined_module_type !== 'undefined' && '' !== defined_module_type ) {
					module_type = defined_module_type;
				}

				if ( typeof module === 'undefined' ) {
					return;
				}

				module_settings = module.attributes;

				// save only synced options for the global modules
				if ( is_saving_global && is_module_type && typeof module_settings['et_pb_global_module'] !== 'undefined' ) {
					unsynced_options = ! _.isEmpty( et_pb_all_unsynced_options[ module_settings['et_pb_global_module'] ] ) ? et_pb_all_unsynced_options[ module_settings['et_pb_global_module'] ] : [];
				}

				for ( var key in module_settings ) {
					if ( ! _.isEmpty( unsynced_options ) ) {
						var global_option_name = $.inArray( key, ['et_pb_content_new', 'et_pb_raw_content'] ) !== -1 ? 'et_pb_content_field' : key.replace( 'et_pb_', '' );

						// skip unsynced options
						if ( $.inArray( global_option_name, unsynced_options ) !== -1 ) {
							continue;
						}
					}

					if ( typeof ignore_global_tag === 'undefined' || 'ignore_global' !== ignore_global_tag || ( typeof ignore_global_tag !== 'undefined' && 'ignore_global' === ignore_global_tag && 'et_pb_global_module' !== key && 'et_pb_global_parent' !== key ) ) {
						if ( typeof ignore_global_tabs === 'undefined' || 'ignore_global_tabs' !== ignore_global_tabs || ( typeof ignore_global_tabs !== 'undefined' && 'ignore_global_tabs' === ignore_global_tabs && 'et_pb_saved_tabs' !== key ) ) {
							var setting_name = key,
								setting_value;

							// remove previous instance of these flags, the bb_built will be added again below as needed
							if ( _.includes( ['et_pb_fb_built', 'et_pb_bb_built', '_address'], setting_name ) ) {
								continue;
							}

							if ( setting_name.indexOf( 'et_pb_' ) === -1 && setting_name !== 'admin_label' ) continue;

							setting_value = typeof( module.get( setting_name ) ) !== 'undefined' ? module.get( setting_name ) : '';

							if ( setting_name === 'et_pb_content_new' || setting_name === 'et_pb_raw_content' ) {
								content = setting_value;

								if ( setting_name === 'et_pb_raw_content' ) {
									// replace the line-breaks with placeholders, so they won't be removed/replaced by WP
									content = content.replace( /\r?\n|\r/g, '<!-- [et_pb_line_break_holder] -->' );
									content = _.escape( content );
								}

								content = $.trim( content );

								if ( '' !== content && setting_name === 'et_pb_content_new' ) {
									content = "\n\n" + content + "\n\n";
								}

							} else if ( setting_value !== '' ) {
								// check if there is a default value for a setting
								if ( typeof module_settings['module_defaults'] !== 'undefined' && typeof module_settings['module_defaults'][ setting_name ] !== 'undefined' ) {
									var module_setting_default = module_settings['module_defaults'][ setting_name ],
										string_setting_value = setting_value + ''; // cast setting value to string to properly compare it with the module_setting_default

									// don't add an attribute to a shortcode, if default value is equal to the current value
									if ( module_setting_default === string_setting_value ) {
										delete module.attributes[ setting_name ];
										continue;
									}
								}

								setting_name = setting_name.replace( 'et_pb_', '' );

								if ( 'et_pb_contact_form' === module_type && setting_name === 'custom_message' ) {
									// save the line breaks in contact message pattern correctly
									setting_value = setting_value.replace( /\r?\n|\r/g, '||et_pb_line_break_holder||' );
								}

								if ( typeof setting_value === 'string' ) {
									// Make sure double quotes are encoded, before adding values to shortcode
									setting_value = setting_value.replace( /\"/g, '%22' ).replace( /\\/g, '%92' );
									setting_value = setting_value.replace( /\[/g, '%91' ).replace( /\]/g, '%93' );
								}

								// escape URLs
								var url_regex = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;

								if ( url_regex.test( setting_value ) ) {
									setting_value = _.escape( _.unescape( setting_value ) );
								}

								// make sure admin label is always on the same position to correctly save the shortcode into library
								if ( 'admin_label' === setting_name ) {
									// save admin label to shortcode only if it's not default
									if ( setting_value !== ET_PageBuilder_Layout.getDefaultAdminLabel( module_settings['module_type'] ) ) {
										attributes = ' ' + setting_name + '="' + setting_value + '"' + attributes;
									}
								} else {
									attributes += ' ' + setting_name + '="' + setting_value + '"';
								}
							}
						}
					}
				}

				template_module_type = 'section' !== module_type && 'row' !== module_type ? 'module' : module_type;
				template_module_type = 'row_inner' === module_type ? 'row' : template_module_type;

				if ( typeof layout_type !== 'undefined' && ( layout_type === template_module_type ) ) {
					attributes += ' template_type="' + layout_type + '"';
				}

				if ( typeof module_settings['template_type'] !== 'undefined' ) {
					attributes += ' template_type="' + module_settings['template_type'] + '"';
				}

				// prefix sections with a bb_built attr flag
				if ( 'section' === module_type ) {
					attributes = ' bb_built="1"' + attributes;
				}

				shortcode = '[' + prefix + module_type + attributes;

				if ( content === '' && ( typeof module_settings['type'] !== 'undefined' && module_settings['type'] === 'module' ) ) {
					open_tag_only = true;
					shortcode += ' /]';
				} else {
					shortcode += ']';
				}

				if ( ! open_tag_only )
					shortcode += content + '[/' + prefix + module_type + ']';

				return shortcode;
			},

			makeSectionsSortable : function() {
				var this_el = this;

				this.$el.sortable( {
					items  : '> *:not(#et_pb_layout_controls, #et_pb_main_container_right_click_overlay, #et-pb-histories-visualizer, #et-pb-histories-visualizer-overlay)',
					cancel : '.et-pb-settings, .et-pb-clone, .et-pb-remove, .et-pb-section-add, .et-pb-row-add, .et-pb-insert-module, .et-pb-insert-column, .et_pb_locked, .et-pb-disable-sort',
					delay: 100,
					update : function( event, ui ) {

						// Loading process occurs. Dragging is temporarily disabled
						if ( ET_PageBuilder_App.isLoading ) {
							this_el.$el.sortable( 'cancel' );
							return;
						}

						// Split Testing adjustment :: section as/has subject/goal
						if ( ET_PageBuilder_AB_Testing.is_active() ) {
							var section_cid = $( ui.item ).children( '.et-pb-section-content' ).attr( 'data-cid' );

							// Check user permission
							if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( section_cid, 'section' ) ) {
								ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
								this_el.$el.sortable( 'cancel' );
								et_reinitialize_builder_layout();
								return;
							}
						}

						// Enable history saving and set meta for history
						this_el.allowHistorySaving( 'moved', 'section' );

						ET_PageBuilder_Events.trigger( 'et-sortable:update' );
					},
					start : function( event, ui ) {
						et_pb_close_all_right_click_options();

						// copy section if Alt key pressed
						if ( event.altKey ) {
							var movedSection = ET_PageBuilder_Layout.getView( $( ui.item ).children('.et-pb-section-content').data( 'cid' ) );

							var view_settings = {
								model      : movedSection.model,
								view       : movedSection.$el,
								view_event : event
							};

							var clone_section = new ET_PageBuilder.RightClickOptionsView( view_settings, true );

							clone_section.copy( event, true );

							clone_section.pasteAfter( event, undefined, undefined, undefined, true, true );

							// Enable history saving and set meta for history
							ET_PageBuilder_App.allowHistorySaving( 'cloned', 'section' );
						}
					}
				} );
			},

			reInitialize : function() {
				var content = et_pb_get_content( 'content' ),
					contentIsEmpty = content == '',
					default_initial_column_type = et_pb_options.default_initial_column_type,
					default_initial_text_module = et_pb_options.default_initial_text_module;

				ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

				this.removeAllSections();

				if ( content.indexOf( '[et_pb_section' ) === -1 ) {
					if ( ! contentIsEmpty ) {
						content = '[et_pb_column type="' + default_initial_column_type + '"][' + default_initial_text_module + ']' + content + '[/' + default_initial_text_module + '][/et_pb_column]';
					}

					content = '[et_pb_section][et_pb_row]' + content + '[/et_pb_row][/et_pb_section]';
				}

				this.createNewLayout( content );

				ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );
			},

			removeAllSections : function( create_initial_layout ) {
				var content;

				// force removal of all the sections and rows
				ET_PageBuilder_Layout.set( 'forceRemove', true );

				this.$el.find( '.et-pb-section-content' ).each( function() {
					var $this_el = $(this),
						this_view = ET_PageBuilder_Layout.getView( $this_el.data( 'cid' ) );

					// don't remove cloned sections
					if ( typeof this_view !== 'undefined' ) {
						// Remove sections. Use remove_all flag so it can differ "remove section" and "clear layout"
						this_view.removeSection( false, true );
					}
				} );

				ET_PageBuilder_Layout.set( 'forceRemove', false );

				if ( create_initial_layout ) {
					content = '[et_pb_section][et_pb_row][/et_pb_row][/et_pb_section]';
					this.createNewLayout( content );
				}
			},

			// creates new layout from any content and saves new shortcodes once
			createNewLayout : function( content, action ) {
				var action = action || '';

				this.stopListening( this.collection, 'change reset add', this.saveAsShortcode );

				if ( action === 'load_layout' && typeof window.switchEditors !== 'undefined' ) {
					content = et_pb_fix_shortcodes( window.switchEditors.wpautop( content ) );
				}

				this.createLayoutFromContent( content );

				this.saveAsShortcode( { et_action : action } );

				this.listenTo( this.collection, 'change reset add', _.debounce( this.saveAsShortcode, 128 ) );
			},

			//replaces the Original element with Replacement element in builder
			replaceElement : function ( original_cid, replacement_view ) {
				var original_view = ET_PageBuilder_Layout.getView( original_cid );

				original_view.$el.after( replacement_view.render().el );

				original_view.model.destroy();

				ET_PageBuilder_Layout.removeView( original_cid );

				original_view.remove();
			},

			showRightClickOptions : function( event ) {
				event.preventDefault();

				var et_right_click_options_view,
					view_settings = {
						model      : {
							attributes : {
								type : 'app',
								module_type : 'app'
							}
						},
						view       : this.$el,
						view_event : event
					};

				et_right_click_options_view = new ET_PageBuilder.RightClickOptionsView( view_settings );
			},

			hideRightClickOptions : function( event ) {
				event.preventDefault();

				et_pb_close_all_right_click_options();
			},

			// calculates the order for each module in the builder
			recalculateModulesOrder : function() {
				var all_modules = this.collection;

				this.order_modules_array = [];
				this.order_modules_array['children_count'] = [];

				// go through all the modules in the builder content and set the module_order attribute for each.
				this.$el.find( '.et_pb_section' ).each( function( index ) {
					var $this_section = $(this).find( '.et-pb-section-content' ),
						section_cid = $this_section.data( 'cid' );

					ET_PageBuilder_App.setModuleOrder( section_cid );
					ET_PageBuilder_App.setModuleAddresses( section_cid, index );

					if ( $this_section.closest( '.et_pb_section' ).hasClass( 'et_pb_section_fullwidth' ) ) {
						$this_section.find( '.et_pb_module_block' ).each( function() {
							var fullwidth_module_cid = $( this ).data( 'cid' );

							ET_PageBuilder_App.setModuleOrder( fullwidth_module_cid );
						} );
					} else if ( $this_section.closest( '.et_pb_section' ).hasClass( 'et_pb_section_specialty' ) ) {
						$this_section.find( '> .et-pb-column' ).each( function() {
							var $this_column = $(this),
								column_cid = $this_column.data( 'cid' );

							ET_PageBuilder_App.setModuleOrder( column_cid );

							if ( $this_column.hasClass( 'et-pb-column-specialty' ) ) {
								// choose each row
								$this_column.find( '.et_pb_row' ).each( function() {
									var $this_row = $(this),
										row_cid = $this_row.find( '.et-pb-row-content' ).data( 'cid' );

									ET_PageBuilder_App.setModuleOrder( row_cid );

									$this_row.find( '.et-pb-column' ).each( function() {
										var $this_column_inner = $(this),
											column_cid = $this_column_inner.data( 'cid' );

										ET_PageBuilder_App.setModuleOrder( column_cid );

										$this_column_inner.find( '.et_pb_module_block' ).each( function() {
											var inner_module_cid = $( this ).data( 'cid' );

											ET_PageBuilder_App.setModuleOrder( inner_module_cid );
										});
									});
								});
							} else {
								// choose each module
								$this_column.find( '.et_pb_module_block' ).each( function() {
									var specialty_module_cid = $( this ).data( 'cid' );

									ET_PageBuilder_App.setModuleOrder( specialty_module_cid, 'specialty' );
								});
							}
						});
					} else {
						$this_section.find( '.et_pb_row' ).each( function() {
							var $this_row = $(this),
								$this_row_content = $this_row.find( '.et-pb-row-content' ),
								row_cid = $this_row_content.data( 'cid' );

							ET_PageBuilder_App.setModuleOrder( row_cid );

							$this_row.find( '.et-pb-column' ).each( function() {
								var $this_column = $(this),
									column_cid = $this_column.data( 'cid' );

								ET_PageBuilder_App.setModuleOrder( column_cid );

								$this_column.find( '.et_pb_module_block' ).each( function() {
									var module_cid = $( this ).data( 'cid' );

									ET_PageBuilder_App.setModuleOrder( module_cid );
								});
							});
						});
					}
				});
			},

			// reload content for the Yoast after it was changed in builder
			updateYoastContent : function() {
				if ( ! et_pb_is_yoast_seo_active() ) {
					return;
				}

				var content = et_pb_get_content( 'content', true );

				// perform the do_shortcode for the current content from builder and force Yoast to reload
				$.ajax( {
					type: "POST",
					url: et_pb_options.ajaxurl,
					data: {
						action : 'et_pb_execute_content_shortcodes',
						et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
						et_pb_unprocessed_data : content
					},
					success: function( data ) {
						et_pb_processed_yoast_content = data;
						YoastSEO.app.pluginReloaded( 'ET_PB_Yoast_Content' );
					}
				} );
			},

			// calculate and add the module_order attribute for the module.
			setModuleOrder: function( cid, is_specialty ) {
				var modules_with_child = $.parseJSON( et_pb_options.et_builder_modules_with_children ),
					current_model,
					module_order,
					parent_row,
					module_type,
					start_from,
					child_slug;

				current_model = ET_PageBuilder_Modules.findWhere( { cid : cid } );

				if ( typeof current_model === 'undefined' ) {
					return;
				}

				module_type = typeof current_model.attributes.module_type !== 'undefined' ? current_model.attributes.module_type : current_model.attributes.type;

				// determine the column type. Check the parent, if parent == row_inner, then column type = column_inner
				if ( 'column' === module_type || 'column_inner' === module_type || 'specialty' === is_specialty ) {
					parent_row = ET_PageBuilder_Modules.findWhere( { cid : current_model.attributes.parent } );

					// inner columns may have column module_type, so check the parent row to determine the column_inner type
					if ( 'column' === module_type && 'row_inner' === parent_row.attributes.module_type ) {
						module_type = 'column_inner';
					}
				}

				// check whether the module order exist for current module_type otherwise set to 0
				module_order = typeof this.order_modules_array[ module_type ] !== 'undefined' ? this.order_modules_array[ module_type ] : 0;

				current_model.attributes.module_order = module_order;

				// reset columns_order attribute to recalculate it properly
				if ( ( 'row' === module_type || 'row_inner' === module_type || 'section' === module_type ) && typeof current_model.attributes.columns_order !== 'undefined' ) {
					current_model.attributes.columns_order = [];
				}

				// columns order should be stored in the Row/Specialty section as well
				if ( 'column' === module_type || 'column_inner' === module_type || 'specialty' === is_specialty ) {
					if ( typeof parent_row.attributes.columns_order !== 'undefined' ) {
						parent_row.attributes.columns_order.push( module_order );
					} else {
						parent_row.attributes.columns_order = [ module_order ];
					}
				}

				// calculate child items for modules which support them and update count in module attributes
				if ( typeof modules_with_child[ module_type ] !== 'undefined' ) {
					child_slug = modules_with_child[ module_type ];
					start_from = typeof this.order_modules_array['children_count'][ child_slug ] !== 'undefined' ? this.order_modules_array['children_count'][ child_slug ] : 0;
					current_model.attributes.child_start_from = start_from; // this attributed used as a start point for calculation of child modules order

					if ( typeof current_model.attributes.et_pb_content_new !== 'undefined' && '' !== current_model.attributes.et_pb_content_new ) {
						var et_pb_shortcodes_tags = ET_PageBuilder_App.getShortCodeChildTags();
						var reg_exp = window.wp.shortcode.regexp( et_pb_shortcodes_tags );
						var matches = current_model.attributes.et_pb_content_new.match( reg_exp );
						start_from += null !== matches ? matches.length : 0;
					}

					this.order_modules_array['children_count'][ child_slug ] = start_from;
				}

				// increment the module order for current module_type
				this.order_modules_array[ module_type ] = module_order + 1;
			},

			setModuleAddresses: function( cid, index, parent_address ) {
				var module   = ET_PageBuilder_Modules.findWhere( { cid : cid } );
				var children = ET_PageBuilder_Modules.where( { parent : cid } );

				if ( _.isUndefined( module ) ) {
					return;
				}

				var module_type     = _.isUndefined( module.attributes.module_type ) ? module.attributes.type : module.attributes.module_type;
				var current_address = module.get( '_address' );
				var new_address     = 'section' === module_type ? index.toString() : parent_address + '.' + index.toString();
				var path            = _.isUndefined( current_address ) ? false : 'et_pb_module_settings_migrations.value_changes.' + current_address;
				var has_migrations  = path ? has( et_pb_options, path ) : false;

				if ( has_migrations && new_address !== current_address ) {
					// Update module settings migration data
					et_pb_options.et_pb_module_settings_migrations.value_changes[new_address] = _.clone( et_pb_options.et_pb_module_settings_migrations.value_changes[current_address] );
					delete et_pb_options.et_pb_module_settings_migrations.value_changes[current_address];
				}

				module.set( { _address: new_address } );

				if ( ! _.isUndefined( children ) && children.length > 0 ) {
					_.forEach( children, function( child_module, child_index ) {
						ET_PageBuilder_App.setModuleAddresses( child_module.attributes.cid, child_index, new_address );
					} );
				}
			},

			updateAdvancedModulesOrder: function( $this_el ) {
				var $modules_container = typeof $this_el !== 'undefined' ? $this_el.find( '.et-pb-option-advanced-module-settings' ) : $( '.et-pb-option-advanced-module-settings' ),
					modules_count = 0,

					$modules_list;

				if ( $modules_container.length ) {
					$modules_list = $modules_container.find( '.et-pb-sortable-options > li' );

					if ( $modules_list.length ) {
						$modules_list.each( function() {
							var $this_item = $( this ),
								this_cid = $this_item.data( 'cid' ),
								current_model,
								current_parent,
								start_from;

							current_model = ET_PageBuilder_Modules.findWhere( { cid : this_cid } );
							current_parent = ET_PageBuilder_Modules.findWhere( { cid : current_model.attributes.parent_cid } );

							start_from = typeof current_parent.attributes.child_start_from !== 'undefined' ? current_parent.attributes.child_start_from : 0;

							current_model.attributes.module_order = modules_count + start_from;

							modules_count++;
						});
					}
				}
			}
		} );

		// Close and remove right click options
		function et_pb_close_all_right_click_options() {
			// Remove right click options UI
			$('#et-builder-right-click-controls').remove();

			// Remove builder overlay (right/left click anywhere outside builder to close right click options UI)
			$('#et_pb_layout_right_click_overlay').remove();
		}

		$('body').on( 'click contextmenu', '#et_pb_layout_right_click_overlay', function( event ){
			event.preventDefault();

			et_pb_close_all_right_click_options();
		});

		function et_pb_is_builder_used() {
			return $('#et_pb_use_builder').val() === 'on';
		}

		function et_pb_activate_upload( $upload_button ) {
			$upload_button.click( function( event ) {
				var $this_el = $(this);

				event.preventDefault();

				et_pb_file_frame = wp.media.frames.et_pb_file_frame = new wp.media.view.MediaFrame.ETSelect({
					title: $this_el.data( 'choose' ),
					library: {
						type: $this_el.data( 'type' )
					},
					button: {
						text: $this_el.data( 'update' ),
					},
					multiple: false
				});

				et_pb_file_frame.on( 'select', function() {
					var url = et_pb_file_frame.state().props.get('url');
					var $upload_field = $this_el.siblings( '.et-pb-upload-field' );

					$upload_field.val( url );
					$upload_field.trigger( 'change' );

					et_pb_generate_preview_content( $this_el );
				});

				et_pb_file_frame.on( 'insert', function() {
					var selectedMediaItem = et_pb_file_frame.state().get('selection').models[0];

					if ( ! _.isUndefined( selectedMediaItem ) ) {
						var $upload_field = $this_el.siblings( '.et-pb-upload-field' );

						$upload_field.val( selectedMediaItem.get('url') );
						$upload_field.trigger( 'change' );

						et_pb_generate_preview_content( $this_el );
					}
				});

				et_pb_file_frame.open();
			} );

			$upload_button.siblings( '.et-pb-upload-field' ).on( 'input', function() {
				et_pb_generate_preview_content( $(this).siblings( '.et-pb-upload-button' ) );
			} );

			$upload_button.siblings( '.et-pb-upload-field' ).each( function() {
				et_pb_generate_preview_content( $(this).siblings( '.et-pb-upload-button' ) );
			} );
		}

		function et_pb_activate_gallery( $gallery_button ) {
			$gallery_button.click( function( event ) {
				var $this_el = $(this),
					$gallery_ids = $gallery_button.closest( '.et-pb-options-tab' ).find( '.et-pb-option-gallery_ids .et-pb-gallery-ids-field' ),
					$gallery_orderby = $gallery_button.closest( '.et-pb-options-tab' ).find( '.et-pb-option-gallery_orderby .et-pb-gallery-ids-field' );

				event.preventDefault();

				// Check if the `wp.media.gallery` API exists.
				if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery )
					return;

				var gallery_ids = $gallery_ids.val().length ? ' ids="' + $gallery_ids.val() + '"' : '',
					gallery_orderby = $gallery_orderby.val().length ? ' orderby="' + $gallery_orderby.val() + '"' : '',
					gallery_shortcode = '[gallery' + gallery_ids + gallery_orderby + ']';

				et_pb_file_frame = wp.media.frames.et_pb_file_frame = wp.media.gallery.edit( gallery_shortcode );

				if ( !gallery_ids ) {
					et_pb_file_frame.setState('gallery-library');
				}

				// Remove the 'Columns' and 'Link To' unneeded settings
				function remove_unneeded_gallery_settings( $el ) {
					setTimeout(function(){
						$el.find( '.gallery-settings' ).find( 'label.setting' ).each(function() {
							if ( $(this).find( '.link-to, .columns, .size' ).length ) {
								$(this).remove();
							} else {
								if ( $(this).has( 'input[type=checkbox]' ).length ) {
									$(this).children( 'input[type=checkbox]' ).css( 'margin', '11px 5px' );
								}
							}
						});
					}, 10 );
				}
				// Remove initial unneeded settings
				remove_unneeded_gallery_settings( et_pb_file_frame.$el );
				// Remove unneeded settings upon re-viewing edit view
				et_pb_file_frame.on( 'content:render:browse', function( browser ){
					remove_unneeded_gallery_settings( browser.$el );
				});

				et_pb_file_frame.state( 'gallery-edit' ).on( 'update', function( selection ) {

					var shortcode_atts = wp.media.gallery.shortcode( selection ).attrs.named;
					if ( shortcode_atts.ids ) {
						$gallery_ids.val( shortcode_atts.ids );
					}

					if ( shortcode_atts.orderby ) {
						$gallery_orderby.val( shortcode_atts.orderby );
					} else {
						$gallery_orderby.val( '' );
					}

				});

			});
		}

		function et_pb_email_lists_select_get_provider( $select ) {
			return $select
				.siblings( 'input' )
				.attr( 'id' )
				.replace( 'et_pb_', '' )
				.replace( '_list', '' );
		}

		function et_pb_email_lists_add_new_account( $accounts_select, settingsView ) {
			var provider      = et_pb_email_lists_select_get_provider( $accounts_select );
			var $container    = $accounts_select.closest( '.et-pb-option--select_with_option_groups' );
			var $account_name = $container.parent().find('.et_pb_account_name:visible');
			var account_name  = $account_name.val();
			var $api_key      = $container.parent().find('.et_pb_api_key:visible');
			var api_key       = $api_key.val();

			function complete( data ) {
				$accounts_select
					.val( $accounts_select.data( 'previous_value' ) )
					.trigger( 'change' );

				$account_name.val( '' );
				$api_key.val( '' );

				ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

				if ( 'error' in data && data.error ) {
					alert( data.message );
				}
			}

			$.ajax({
				type: 'POST',
				url: et_pb_options.ajaxurl,
				data: {
					action: 'et_builder_email_add_account',
					et_builder_email_add_account_nonce: et_pb_options.et_builder_email_add_account_nonce,
					et_provider: provider,
					et_account: account_name,
					et_api_key: api_key,
					et_bb: true
				}

			}).done( function( data, status, response ) {
				data = JSON.parse( data );

				$accounts_select.html( _.template( data.accounts_list )( settingsView.model.attributes ) );
				complete( data );

			}).fail( complete );
		}

		function et_pb_email_fetch_lists( $accounts_select, $button, settingsView ) {
			var provider     = et_pb_email_lists_select_get_provider( $accounts_select );
			var account_name = $accounts_select.data( 'selected_account' );

			function complete( data ) {
				$accounts_select
					.val( $accounts_select.data( 'previous_value' ) )
					.trigger( 'change' );

				$button.removeClass( 'fetch_lists_in_progress' );
				ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

				if ( 'error' in data && data.error ) {
					alert( data.message );
				}
			}

			$.ajax({
				type: 'POST',
				url: et_pb_options.ajaxurl,
				data: {
					action: 'et_builder_email_get_lists',
					et_builder_email_fetch_lists_nonce: et_pb_options.et_builder_email_fetch_lists_nonce,
					et_provider: provider,
					et_account: account_name,
					et_bb: true
				}

			}).done( function( data, status, response ) {
				data = JSON.parse( data );

				$accounts_select.html( _.template( data.accounts_list )( settingsView.model.attributes ) );
				complete( data );

			}).fail( complete );
		}

		function et_pb_email_remove_account( $accounts_select, settingsView ) {
			var provider     = et_pb_email_lists_select_get_provider( $accounts_select );
			var account_name = $accounts_select.data( 'selected_account' );

			function complete( data ) {
				$accounts_select
					.val( 'none' )
					.trigger( 'change' );

				ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

				if ( 'error' in data && data.error ) {
					alert( data.message );
				}
			}

			$.ajax({
				type: 'POST',
				url: et_pb_options.ajaxurl,
				data: {
					action: 'et_builder_email_remove_account',
					et_builder_email_remove_account_nonce: et_pb_options.et_builder_email_remove_account_nonce,
					et_provider: provider,
					et_account: account_name,
					et_bb: true
				}

			}).done( function( data, status, response ) {
				data = JSON.parse( data );

				$accounts_select.html( _.template( data.accounts_list )( settingsView.model.attributes ) );
				complete( data );

			}).fail( complete );
		}

		function et_pb_email_setup_submit_cancel_buttons( $buttons, $accounts_select, settingsView ) {
			$buttons.on( 'click', function() {
				if ( $(this).hasClass( 'et_pb_email_submit' ) ) {
					// Submit button
					ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );
					et_pb_email_lists_add_new_account( $accounts_select, settingsView );
				} else {
					// Cancel button
					$accounts_select
						.val( $accounts_select.data( 'previous_value' ) )
						.trigger( 'change' );
				}
			});

			$buttons.appendTo( $buttons.parent() );
		}

		function et_pb_email_lists_buttons_setup( $add_account_buttons, settingsView ) {
			$add_account_buttons.each( function() {
				var $add_account_button    = $(this);
				var $accounts_select       = $add_account_button.siblings( 'select' );
				var $fetch_lists_button    = $add_account_button.siblings( '.et_pb_email_force_fetch_lists' );
				var $remove_account_button = $add_account_button.siblings( '.et_pb_email_remove_account' );
				var $value_field           = $(this).siblings( 'input' );
				var affects                = $value_field.data( 'affects' ).split(', ');
				var $affects;

				affects  = _.map( affects, function( affect ) { return '#et_pb_' + affect; } ).join( ', ' );
				$affects = $(this).closest( '.et-pb-options-tab' ).find( affects );

				$affects.one( 'et-pb-option-field-shown', function() {
					var $buttons = $(this).siblings( 'button' );
					et_pb_email_setup_submit_cancel_buttons( $buttons, $accounts_select, settingsView );
				});

				$fetch_lists_button.data( 'original_text', $fetch_lists_button.text() );
				$remove_account_button.data( 'original_text', $remove_account_button.text() );

				$accounts_select.on( 'change', function() {
					var value = $(this).val();

					if ( 'add_new_account' === value && ! $accounts_select.parent().hasClass( 'new_account_in_progress' ) ) {
						$accounts_select.parent().addClass( 'new_account_in_progress' );

						$('<span>')
							.text( $accounts_select.data( 'adding_new_account_text' ) )
							.insertAfter( $accounts_select )
							.addClass( 'et_pb_email_account_message' );

						$add_account_button
							.hide()
							.siblings()
							.not( 'span' )
							.hide();

						if ( $accounts_select.siblings( '#et_pb_aweber_list' ).length > 0 ) {
							window.open( 'https://auth.aweber.com/1.0/oauth/authorize_app/b17f3351' );
						}

					} else if ( 'fetch_lists' === value && ! $accounts_select.parent().hasClass( 'fetch_lists_in_progress' ) ) {
						$accounts_select.parent().addClass( 'fetch_lists_in_progress' );
						ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );
						et_pb_email_fetch_lists( $accounts_select, $add_account_button, settingsView );

					} else if ( 'remove_account' === value && ! $accounts_select.parent().hasClass( 'remove_account_in_progress' )) {
						$accounts_select.parent().addClass( 'remove_account_in_progress' );

						$add_account_button
							.hide()
							.siblings('p')
							.hide();

						$remove_account_button.text( $remove_account_button.data( 'confirm_text' ) );
						$fetch_lists_button.text( $fetch_lists_button.data( 'cancel_text' ) );

						var msg = $accounts_select.data( 'confirm_remove_text' ) + ' ' + $accounts_select.data( 'selected_account' );

						$('<span>')
							.text( msg )
							.insertAfter( $accounts_select )
							.addClass( 'et_pb_email_account_message' );

						$accounts_select.hide();

					} else {
						$accounts_select.parent().removeClass( 'new_account_in_progress remove_account_in_progress fetch_lists_in_progress' );

						var $modal_save_buttons = settingsView.$el.prev().find( '.et-pb-modal-save, .et-pb-modal-save-template' );
						$modal_save_buttons.css( 'cursor', '' );

						if ( 'none' === value ) {
							$accounts_select.parent().addClass( 'no_account_selected' );
						} else {
							$accounts_select.parent().removeClass( 'no_account_selected' );
						}

						$add_account_button.show();

						$remove_account_button.text( $remove_account_button.data( 'original_text' ) );

						$fetch_lists_button.text( $fetch_lists_button.data( 'original_text' ) );

						if ( ! value || 'none' === value ) {
							$remove_account_button.hide();
							$fetch_lists_button.hide();
						} else {
							$remove_account_button.show();
							$fetch_lists_button.show();
						}

						$add_account_button.siblings('span').remove();
						$add_account_button.siblings('p').show();

						$accounts_select.show();
					}
				});

				$add_account_button.click( function() {
					var $modal_save_buttons = settingsView.$el.prev().find( '.et-pb-modal-save, .et-pb-modal-save-template' );
					$modal_save_buttons.css( 'cursor', 'not-allowed' );

					$accounts_select
						.data( 'previous_value', $accounts_select.val() )
						.val( 'add_new_account' )
						.trigger( 'change' );
				});

				$fetch_lists_button.click( function() {
					if ( $accounts_select.parent().hasClass( 'remove_account_in_progress' ) ) {
						// Cancel button clicked
						$accounts_select
							.val( $accounts_select.data( 'previous_value' ) )
							.trigger( 'change' );

						return;
					}

					var account = $accounts_select
						.find(':selected')
						.parent()
						.attr('label');

					$accounts_select.data( 'selected_account', account );
					$accounts_select
						.data( 'previous_value', $accounts_select.val() )
						.val( 'fetch_lists' )
						.trigger( 'change' );
				});

				$remove_account_button.click( function() {
					if ( $accounts_select.parent().hasClass( 'remove_account_in_progress' ) ) {
						ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );
						et_pb_email_remove_account( $accounts_select, settingsView );
						return;
					}

					var account = $accounts_select
						.find(':selected')
						.parent()
						.attr('label');

					if ( ! account ) {
						return;
					}

					$accounts_select.data( 'selected_account', account );

					$accounts_select
						.data( 'previous_value', $accounts_select.val() )
						.val( 'remove_account' )
						.trigger( 'change' );
				});
			});
		}

		function et_pb_generate_video_image( $video_image_button ) {
			$video_image_button.click( function( event ) {
				var $this_el = $(this),
					$upload_field = $( '#et_pb_src.et-pb-upload-field' ),
					video_url = $upload_field.val().trim();

				event.preventDefault();

				$.ajax( {
					type: "POST",
					url: et_pb_options.ajaxurl,
					data:
					{
						action : 'et_pb_video_get_oembed_thumbnail',
						et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
						et_video_url : video_url
					},
					success: function( response ) {
						if ( response.length ) {
							$('#et_pb_image_src').val( response ).trigger('input');
						} else {
							$this_el.after( '<div class="et-pb-error">' + et_pb_options.video_module_image_error + '</div>' );
							$this_el.siblings('.et-pb-error').delay(5000).fadeOut(800);
						}

					}
				} );
			} );
		}

		function et_pb_generate_preview_content( $upload_button ){
			if ( $upload_button.length < 1 ) {
				return;
			}

			var $upload_field = $upload_button.siblings( '.et-pb-upload-field' ),
				$preview = $upload_field.siblings( '.et-pb-upload-preview' ),
				$background_preview = $upload_field.closest( '.et-pb-option' ).find( '.et-pb-option-preview' ),
				has_preview = $background_preview.length,
				image_url = $upload_field.val().trim(),
				type = $upload_button.data( 'type' );

			if ( type !== 'image' && ! ( type === 'video' && has_preview ) ) return;

			if ( image_url === '' ) {
				if ( has_preview ) {
					$background_preview.addClass( 'et-pb-option-preview--empty' ).find('.et-pb-preview-content').remove();

					return;
				} else if ( $preview.length ) {
					$preview.remove();
				}

				return;
			}

			if ( has_preview ) {
				$background_preview.find('.et-pb-preview-content').remove();

				var $background_container = $upload_button.closest( '.et-pb-option-container--background' ),
					$background_settings_fields = $background_container.find('input, select'),
					column_index = $background_container.attr('data-column-index'),
					background_images = [ 'url(' + image_url + ')' ],
					gradient_backround = et_pb_get_gradient( $background_container.find( '.et_pb_background-tab--gradient' ) ),
					module_type = $upload_button.closest( '.et_pb_module_settings' ).attr( 'data-module_type' ),
					background_settings = {},
					preview_content = '',
					has_background_gradient = false,
					has_background_image = false;

				if ( gradient_backround ) {
					has_background_gradient = true;
					background_images.push( gradient_backround );
				}

				$background_settings_fields.each(function() {
					if ( typeof $(this).attr('id') !== 'undefined' ) {
						var name          = $(this).attr( 'id' ).replace( 'et_pb_', '' ),
							value         = $(this).val(),
							default_value = typeof $(this).attr( 'data-default' ) !== 'undefined' ? $(this).attr( 'data-default' ) : '',
							has_value     = value !== '' && typeof value !== 'undefined';

						// Column adjustment
						if ( typeof column_index !== 'undefined' ) {
							name = name.replace( '_' + column_index, '' );
						}

						background_settings[ name ] = has_value ? value : default_value;
					}
				});

				if ( type === 'image' ) {
					var is_parallax = module_type === 'et_pb_post_title' ? background_settings.parallax_effect === 'on' : background_settings.parallax === 'on',
						imageStyle = {
							position: 'absolute',
							top: 0,
							right: 0,
							bottom: 0,
							left: 0,
						};

					if ( is_parallax ) {
						imageStyle.backgroundImage = 'url(' + image_url + ')';
						imageStyle.backgroundRepeat = 'no-repeat';
						imageStyle.backgroundSize = 'cover';
						imageStyle.backgroundPosition = 'center';
					} else {
						has_background_image = true;
						imageStyle.backgroundImage = background_images.join(', ');
						imageStyle.backgroundSize = background_settings.background_size;
						imageStyle.backgroundPosition = background_settings.background_position.replace( '_', ' ' );
						imageStyle.backgroundRepeat = background_settings.background_repeat;
						imageStyle.backgroundBlendMode = background_settings.background_blend;

						if ( has_background_gradient && has_background_image ) {
							imageStyle.backgroundColor = 'initial';
						} else {
							imageStyle.backgroundColor = background_settings.background_color;
						}
					}

					preview_content = $('<div />', {
						class: 'et-pb-preview-content'
					}).css( imageStyle );
				} else if ( type === 'video' ) {
					preview_content = $('<video />', {
						class: 'et-pb-preview-content',
						src: image_url,
						loop: true,
						controls: true,
						height: 190
					} );
				}

				$background_preview.removeClass( 'et-pb-option-preview--empty' ).prepend( preview_content );
			} else if ( ! $preview.length ) {
				$upload_button.siblings('.description').before( '<div class="et-pb-upload-preview">' + '<strong class="et-pb-upload-preview-title">' + et_pb_options.preview_image + '</strong>' + '<img src="" width="408" /></div>' );
				$preview = $upload_field.siblings( '.et-pb-upload-preview' );
			}

			$preview.find( 'img' ).attr( 'src', image_url );
		}

		function et_pb_reposition_colorpicker_element( $colorpicker ) {
			var $colorpicker_container = $colorpicker.closest( '.et-pb-option-container' ),
				handle_width           = parseInt( $colorpicker_container.find( '.iris-square-value.ui-draggable' ).width() ),
				handle_left            = parseInt( $colorpicker_container.find( '.iris-square-value.ui-draggable' ).css( 'left' ) ) + ( handle_width / 2 ),
				container_width        = parseInt( $colorpicker_container.width() ),
				iris_strip_right_max   = 50;

			if ( $colorpicker !== '' && handle_left > ( container_width - iris_strip_right_max ) ) {
				$colorpicker_container.addClass( 'on-right-corner' );
			} else {
				$colorpicker_container.removeClass( 'on-right-corner' );
			}
		}

		var ET_PageBuilder_Events = _.extend( {}, Backbone.Events ),

			ET_PageBuilder_Layout = new ET_PageBuilder.Layout,

			ET_PageBuilder_Modules = new ET_PageBuilder.Modules,

			ET_PageBuilder_Histories = new ET_PageBuilder.Histories,

			ET_PageBuilder_App = new ET_PageBuilder.AppView( {
				model : ET_PageBuilder.Module,
				collection : ET_PageBuilder_Modules,
				history : ET_PageBuilder_Histories
			} ),

			ET_PageBuilder_Visualize_Histories = new ET_PageBuilder.visualizeHistoriesView,

			$et_pb_content = $( '#et_pb_hidden_editor' ),

			et_pb_content_html = $et_pb_content.html(),

			et_pb_file_frame,

			$toggle_builder_button = $('#et_pb_toggle_builder'),

			$toggle_builder_button_wrapper = $('.et_pb_toggle_builder_wrapper'),

			$builder = $( '#et_pb_layout' ),

			$et_pb_old_content = $('#et_pb_old_content'),

			$post_format_wrapper = $('#formatdiv'),

			$use_builder_custom_field = $( '#et_pb_use_builder' ),

			$main_editor_wrapper = $( '#et_pb_main_editor_wrap' ),

			$et_pb_setting = $( '.et_pb_page_setting' ),

			$et_pb_layout_settings = $( '.et_pb_page_layout_settings' ),

			$et_pb_templates_cache = [],

			et_pb_globals_requested = 0,

			et_pb_globals_loaded = 0,

			et_pb_processed_yoast_content = false,

			et_pb_quick_tags_init_done = {};

		ET_PageBuilder.Events = ET_PageBuilder_Events;

		var ET_PageBuilder_AB_Testing = {
			is_active : function () {
				return $( '#et_pb_use_ab_testing' ).length && 'on' === $( '#et_pb_use_ab_testing' ).val() ? true : false;
			},

			toggle_status : function( status ) {
				var $input = $( '#et_pb_use_ab_testing' ),
					inputValue = $input.val(),
					status = _.isUndefined( status ) ? false : status;

				if ( ( inputValue === 'on' && ! status ) || ( ( inputValue === 'off' || inputValue === '' ) && status ) ) {
					$input.addClass( 'et_pb_value_updated' );
				}

				set_this_editor_in_use();

				if ( status ) {
					$input.val( 'on' );
					this.toggle_portability( false );
				} else {
					$input.val( 'off' );
				}
			},

			toggle_portability : function( status ) {
				var $portability_button   = $( '.et-pb-app-portability-button' ),
					disable_class         = 'et-core-disabled',
					is_currently_disabled = $portability_button.hasClass( disable_class );

				// If no explicit status passed, do toggling
				if ( _.isUndefined( status ) ) {
					status = is_currently_disabled ? true : false;
				}

				// false === disabling
				if ( status ) {
					$portability_button.removeClass( disable_class );
				} else {
					$portability_button.addClass( disable_class );
				}
			},

			get_stats_refresh_interval : function () {
				return $( '#et_pb_ab_stats_refresh_interval' ).length ? $( '#et_pb_ab_stats_refresh_interval' ).val() : 'hourly';
			},

			get_shortcode_tracking_status : function() {
				return $( '#et_pb_enable_shortcode_tracking' ).length && '' !== $( '#et_pb_enable_shortcode_tracking' ).val() ? $( '#et_pb_enable_shortcode_tracking' ).val() : 'off';
			},

			is_active_based_on_models : function () {
				var subjects = ET_PageBuilder_Modules.where({ et_pb_ab_subject : 'on' }),
					goal     = ET_PageBuilder_Modules.where({ et_pb_ab_goal : 'on'});

				return ( subjects.length > 1 && goal.length > 0 );
			},

			has_permission : function () {
				if ( et_pb_ab_js_options.has_permission === '1' ) {
					return true;
				} else {
					return false;
				}
			},

			check_create_db : function () {
				if ( 'exists' !== et_pb_options.ab_db_status ) {
					$.ajax( {
						type: "POST",
						url: et_pb_options.ajaxurl,
						data:
						{
							action : 'et_pb_create_ab_tables',
							et_pb_ab_nonce : et_pb_options.ab_testing_builder_nonce,
						},
						success: function( response ) {
							if ( ! response.length || 'success' !== response ) {
								return false;
							}

							et_pb_options.ab_db_status = 'exists';
						}
					} );
				}

				return true;
			},

			is_selecting_subject : function() {
				if ( ! _.isUndefined( ET_PageBuilder_App.is_selecting_ab_testing_subject ) && ET_PageBuilder_App.is_selecting_ab_testing_subject === true ) {
					return true;
				} else {
					return false;
				}
			},

			is_selecting_goal : function() {
				if ( ! _.isUndefined( ET_PageBuilder_App.is_selecting_ab_testing_goal ) && ET_PageBuilder_App.is_selecting_ab_testing_goal === true ) {
					return true;
				} else {
					return false;
				}
			},

			is_selecting_winner : function() {
				if ( ! _.isUndefined( ET_PageBuilder_App.is_selecting_ab_testing_winner ) && ET_PageBuilder_App.is_selecting_ab_testing_winner === true ) {
					return true;
				} else {
					return false;
				}
			},

			is_selecting : function() {
				if ( this.is_selecting_subject() || this.is_selecting_goal() || this.is_selecting_winner() ) {
					return true;
				} else {
					return false;
				}
			},

			is_subject : function( model ) {
				if ( this.is_active() && ! ET_PageBuilder_Layout.is_app( model ) && model.has( 'et_pb_ab_subject' ) && model.get( 'et_pb_ab_subject' ) === 'on' ) {
					return true;
				}

				return false;
			},

			is_subject_children : function( model ) {
				var parent_views = ET_PageBuilder_Layout.getParentViews( model.attributes.parent ),
					result = false;

				if ( ! _.isEmpty( parent_views ) ) {
					_.each( parent_views, function( parent_view ) {
						if ( ! _.isUndefined( parent_view.model.get( 'et_pb_ab_subject' ) ) && parent_view.model.get( 'et_pb_ab_subject' ) === 'on' ) {
							result = true;
						}
					} );
				}

				return result;
			},

			is_unremovable_subject : function( model ) {
				if ( this.is_active() && this.is_subject( model ) && this.subjects().length < 3 ) {
					return true;
				} else {
					return false;
				}
			},

			is_goal : function( model ) {
				if ( this.is_active() && ! ET_PageBuilder_Layout.is_app( model ) && model.has( 'et_pb_ab_goal' ) && model.get( 'et_pb_ab_goal' ) === 'on' ) {
					return true;
				}

				return false;
			},

			is_goal_children : function ( model ) {
				var parent_views = ET_PageBuilder_Layout.getParentViews( model.attributes.parent ),
					result = false;

				if ( ! _.isEmpty( parent_views ) ) {
					_.each( parent_views, function( parent_view ) {
						if ( ! ET_PageBuilder_Layout.is_app( model ) && parent_view.model.has( 'et_pb_ab_goal' ) && parent_view.model.get( 'et_pb_ab_goal' ) === 'on' ) {
							result = true;
						}
					} );
				}

				return result;
			},

			is_user_has_permission : function( cid, context, model ) {
				if ( ! cid ) {
					return false;
				}

				// Get view
				var view                = ET_PageBuilder_Layout.getView( cid ),
					model               = _.isUndefined( model ) ? view.model : model,
					has_permission      = ET_PageBuilder_AB_Testing.has_permission(),
					is_subject          = ET_PageBuilder_AB_Testing.is_subject( model ),
					is_subject_children = ET_PageBuilder_AB_Testing.is_subject_children( model ),
					has_subject         = ET_PageBuilder_AB_Testing.has_subject( model ),
					is_goal             = ET_PageBuilder_AB_Testing.is_goal( model ),
					is_goal_children    = ET_PageBuilder_AB_Testing.is_goal_children( model ),
					has_goal            = ET_PageBuilder_AB_Testing.has_goal( model ),
					status;

				if ( context === 'section' ) {
					status = is_subject || has_subject || is_goal || has_goal;
				} else if ( context === 'module' ) {
					status = is_subject || is_subject_children || is_goal || is_goal_children;
				} else if ( context === 'add_module' ) {
					status = is_subject || is_subject_children || is_goal || is_goal_children;
				} else if ( context === 'add_row' ) {
					status = is_subject_children || is_goal_children;
				} else if ( context === 'paste' ) {
					status = is_subject_children || is_goal_children;
				} else if ( context === 'copy' ) {
					status = is_subject || has_subject || is_goal || has_goal;
				} else {
					status = is_subject || is_subject_children || has_subject || is_goal || is_goal_children || has_goal;
				}

				// User with no ab_testing permisson cannot modify Split testing-related item
				if ( ! has_permission && status ) {
					return false;
				}

				return true;
			},

			is_split_test_item : function( model ) {
				if (
					this.is_subject( model ) ||
					this.is_subject_children( model ) ||
					this.has_subject( model ) ||
					this.is_goal( model ) ||
					this.is_goal_children( model ) ||
					this.has_goal( model )
				) {
					return true;
				}

				return false;
			},

			filter_goals : function( models ) {
				var goals = _.filter( models, function( model ) {
					if ( model.has( 'et_pb_ab_goal' ) && model.get( 'et_pb_ab_goal' ) === 'on' ) {
						return true;
					}

					return false;
				});

				return goals;
			},

			filter_subjects : function( models ) {
				var subjects = _.filter( models, function( model ) {
					if ( model.has( 'et_pb_ab_subject' ) && model.get( 'et_pb_ab_subject' ) === 'on' ) {
						return true;
					}

					return false;
				});

				return subjects;
			},

			filter_models_by_cids : function( cids, models ) {
				var filtered_models;

				models = _.isUndefined( models ) ? ET_PageBuilder_Modules.models : models;

				filtered_models = _.filter( models, function( model ) {
					if ( $.inArray( model.get( 'parent' ), cids ) !== -1 ) {
						return true;
					}

					return false;
				});

				return filtered_models;
			},

			pluck_cids_from_models : function( models ) {
				var cids = [];

				_.each( models, function( model ){
					cids.push( model.get( 'cid' ) );
				});

				return cids;
			},

			has_goal : function ( model ) {
				var cid = typeof model.get === 'function' ? model.get( 'cid' ) : false,
					has_goal = false;

				if ( this.is_active() && cid !== false ) {
					_.each( ET_PageBuilder_Layout.getChildrenViews( cid ), function( child_view ) {
						if ( child_view.model.get( 'et_pb_ab_goal' ) === 'on' ) {
							has_goal = true;
						}
					});
				}

				return has_goal;
			},

			has_subject : function( model ) {
				var cid = typeof model.get === 'function' ? model.get( 'cid' ) : false,
					has_subject = false;

				if ( this.is_active() ) {
					_.each( ET_PageBuilder_Layout.getChildrenViews( cid ), function( child_view ) {
						if ( child_view.model.get( 'et_pb_ab_subject' ) === 'on' ) {
							has_subject = true;
						}
					});
				}

				return has_subject;
			},

			has_unremovable_subject : function( model ) {
				var cid = model.get( 'cid' ),
					type = model.get( 'type' ),
					rows,
					row_is_subject,
					rows_cid = [],
					row_inner,
					row_inner_is_goal,
					columns,
					columns_cid = [],
					modules,
					module_is_subject;

				if ( this.is_active() ) {

					if ( type === 'section' ) {
						// Get row's models
						rows = ET_PageBuilder_Modules.where({ parent : cid } );

						// Look for row as subjects
						row_is_subject = this.filter_subjects( rows );

						// Return true if this section gets deleted, remaining subject will be less than two
						if ( ( this.count_subjects() - row_is_subject.length ) < 2 ) {
							return true;
						}

						// Get row's cids
						rows_cid = this.pluck_cids_from_models( rows );

						// Specialty Section's adjustment :: it has one level deep tp get into rows_cid
						if ( ! _.isUndefined( model.get( 'et_pb_specialty' ) ) && model.get( 'et_pb_specialty' ) === 'on' ) {
							// Look for row_inner models
							row_inner = this.filter_models_by_cids( rows_cid );

							// Look for row_inner as subject
							row_inner_is_subject = this.filter_subjects( row_inner );

							// Return true if this row_inner gets deleted, remaining subject will be less than two
							if ( ( this.count_subjects() - row_inner_is_subject.length ) < 2 ) {
								return true;
							}

							// Passes row_inner cids as row cids
							rows_cid = this.pluck_cids_from_models( row_inner );
						}
					}

					if ( type === 'row' || type === 'row_inner' ) {
						// Set cid as row cids
						rows_cid = [cid];
					}

					if ( type === 'section' || type === 'row_inner' || type === 'row' ) {
						// Get column's models
						columns = this.filter_models_by_cids( rows_cid );

						// Get column's cids
						columns_cid = this.pluck_cids_from_models( columns );

						// Get module's models
						modules = this.filter_models_by_cids( columns_cid );

						// Look for module as subject
						module_is_subject = this.filter_subjects( modules );

						// Return true if this row gets deleted, remaining subject will be less than two
						if ( ( this.count_subjects() - module_is_subject.length ) < 2 ) {
							return true;
						}
					}
				}

				return false;
			},

			subjects : function () {
				var subjects = ET_PageBuilder_Modules.where({ et_pb_ab_subject : 'on' })

				return subjects;
			},

			subject_ids : function () {
				var subjects = this.subjects(),
					subject_ids = [];

				if ( subjects.length > 0 ) {
					_.each( subjects, function( subject ) {
						if ( subject.has( 'et_pb_ab_subject_id' ) ) {
							subject_ids.push( subject.get( 'et_pb_ab_subject_id' ) );
						}
					} );
				}

				return subject_ids;
			},

			count_subjects : function() {
				return this.subjects().length;
			},

			get_subject_id : function() {
				if ( 0 === this.count_subjects() ) {
					return 0;
				}

				var all_subjects = this.subjects(),
					subject_ids = [];

				_.each( all_subjects, function( subject ) {
					if ( subject.has( 'et_pb_ab_subject_id' ) ) {
						subject_ids.push( parseInt( subject.get( 'et_pb_ab_subject_id' ) ) );
					} else {
						subject_ids.push( 0 );
					}
				} );

				return ( Math.max.apply( Math, subject_ids ) + 1 ).toString();
			},

			set_subject : function( view, waiting ) {
				var that = this;

				// make sure ab testing database tables created otherwise wait until creating process is finished
				if ( 'exists' !== et_pb_options.ab_db_status ) {
					setTimeout( function() {
						that.set_subject( view, 'waiting' );
					}, 500 );

					ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

					return;
				}

				// finish the loading gif if it was started
				if ( typeof waiting !== 'undefined' && 'waiting' === waiting ) {
					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );
				}

				// Assign attribute
				view.model.set( 'et_pb_ab_subject', 'on' );
				view.model.set( 'et_pb_ab_subject_id', that.get_subject_id() );

				// Mark as selected
				view.$el.addClass( 'et_pb_ab_subject' );

				// Turn off subject selecting mode
				ET_PageBuilder_App.is_selecting_ab_testing_subject = false;
				$( '#et_pb_layout' ).removeClass( 'et_pb_select_ab_testing_subject' );

				// Turn on goal selecting mode if needed
				if ( ET_PageBuilder_AB_Testing.count_subjects() < 2 ) {
					ET_PageBuilder_App.is_selecting_ab_testing_goal = true;

					$( '#et_pb_layout' ).addClass( 'et_pb_select_ab_testing_goal' );

					ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_goal' );
				}

				// Update shortcode layout
				ET_PageBuilder_App.saveAsShortcode();
			},

			set_subject_rank_coloring : function( view ) {
				var subjects_rank = et_pb_ab_js_options.subjects_rank,
					subject_id = view.model.has( 'et_pb_ab_subject_id' ) ? 'subject_' + view.model.get( 'et_pb_ab_subject_id' ) : false,
					type = view.model.get( 'type' ),
					title_selector = '.et-pb-module-title';

				// Adjust title selector
				switch( type ) {
					case 'section' :
						title_selector = '.et-pb-section-title';
						break;
					case 'row_inner' :
						title_selector = '.et-pb-row-title';
						break;
					case 'row' :
						title_selector = '.et-pb-row-title';
						break;
				}

				if ( subject_id && ! _.isUndefined( subjects_rank[subject_id] ) && ! _.isUndefined( subjects_rank[subject_id]['rank'] ) && ! _.isUndefined( subjects_rank[subject_id]['percentage'] ) ) {

					view.$el.addClass( 'rank-' + subjects_rank[subject_id]['rank'] );

					view.$el.find(title_selector).append( " (" + subjects_rank[subject_id]['percentage'] + ")" );
				}

				// Add subject ID marker
				if ( view.model.has( 'et_pb_ab_subject_id' )  ) {
					if ( type === 'module' ) {
						view.$el.find('.et-pb-ab-subject-id').remove();
						view.$el.find('.et-pb-remove').after( $('<span />', { class : 'et-pb-ab-subject-id' } ).text( view.model.get( 'et_pb_ab_subject_id' ) ) );
					} else {
						view.$el.find('.et-pb-ab-subject-id').remove();
						view.$el.find(title_selector).append( $('<span />', { class : 'et-pb-ab-subject-id' } ).text( view.model.get( 'et_pb_ab_subject_id' ) ) );
					}
				}
			},

			set : function ( view, event ) {
				if ( this.is_selecting_subject() ) {

					// Disguiese all global-item related attributes into while being used as Split test
					if ( ET_PageBuilder_Layout.is_global( view.model ) || ET_PageBuilder_Layout.is_global_children( view.model ) ) {
						ET_PageBuilder_Layout.removeGlobalAttributes( view, true );
					}

					this.set_subject( view );

					setTimeout( function() {
						ET_PageBuilder_App.disable_publish = true;
						$( '#publish' ).addClass( 'disabled' );
					}, 750 );

					return;
				} else if ( this.is_selecting_goal() ) {
					// Prevent row / section of selected subject to be set as subject
					if ( this.has_subject( view.model ) ) {
						this.alert( 'cannot_select_subject_parent_as_goal' );
						return;
					}

					// Mark as doing combination. This force disables enable_history
					ET_PageBuilder_App.is_doing_combination = true;

					// Disguise all global-item related attributes while being used as Split test goal
					// global item behaviour removes Split testing goal attributes when its setting modal
					// saves modified configuration
					if ( ET_PageBuilder_Layout.is_global( view.model ) || ET_PageBuilder_Layout.is_global_children( view.model ) ) {
						ET_PageBuilder_Layout.removeGlobalAttributes( view, true );
					}

					// Assign attribute
					view.model.set( 'et_pb_ab_goal', 'on' );

					// Mark as selected
					view.$el.addClass( 'et_pb_ab_goal' );

					// Turn off goal selecting mode
					ET_PageBuilder_App.is_selecting_ab_testing_goal = false;
					$( '#et_pb_layout' ).removeClass( 'et_pb_select_ab_testing_goal' );

					// update post meta with the Goal Module slug
					$( '#et_pb_ab_goal_module' ).val( view.options.model.attributes.module_type );

					// Duplicate subject
					var subject_model = ET_PageBuilder_Modules.findWhere({ et_pb_ab_subject : 'on' }),
						subject_view = _.isUndefined( subject_model.cid ) ? false : ET_PageBuilder_Layout.getView( subject_model.get( 'cid' ) );

					if ( subject_view ) {
						var clone_subject,
							view_settings = {
								model      : subject_view.model,
								view       : subject_view.$el,
								view_event : event
							};

						clone_subject = new ET_PageBuilder.RightClickOptionsView( view_settings, true );

						clone_subject.copy( event );

						clone_subject.pasteAfter( event );

						ET_PageBuilder_AB_Testing.alert( 'configure_ab_testing_alternative' );

						// Update Split Testing Status
						this.update();
					}

					// Update shortcode layout & rebuild the UI
					et_reinitialize_builder_layout();

					// Wait until et_reinitialize_builder_layout() sequences have been finished
					// to disable is_doing_combination status
					setTimeout( function() {
						ET_PageBuilder_App.is_doing_combination = false;

						ET_PageBuilder_App.allowHistorySaving( 'turnon', 'abtesting' );

						// Display view stats icon
						$( '#et_pb_layout .et-pb-app-view-ab-stats-button' ).addClass( 'active' );

						delete ET_PageBuilder_App.disable_publish;
						$( '#publish' ).removeClass( 'disabled' );
					}, 650 );

					return;
				} else if ( this.is_selecting_winner() ) {
					if ( view.options.model.has( 'et_pb_ab_subject' ) && view.options.model.get( 'et_pb_ab_subject' ) === 'on' ) {
						// Find other blocks model then remove it
						var ab_testing_subjects = ET_PageBuilder_Modules.where({ et_pb_ab_subject : 'on' }),
							view_type,
							view;

						// Mark as doing combination. This force disables enable_history
						ET_PageBuilder_App.is_doing_combination = true;

						// Turn off Split testing's winner selecting mode
						ET_PageBuilder_App.is_selecting_ab_testing_winner = false;

						// Loop subjects
						_.each( ab_testing_subjects, function( subject ) {

							// Remove unselected subject
							if ( view.model.attributes.cid !== subject.attributes.cid ) {
								subject_view = ET_PageBuilder_Layout.getView( subject.attributes.cid );
								view_type = subject_view.model.get( 'type' );

								if ( view_type === 'section' ) {
									subject_view.removeSection();
								} else if ( view_type === 'row' || view_type === 'row_inner' ) {
									subject_view.removeRow();
								} else if ( view_type === 'module' ) {
									subject_view.removeModule();
								}
							}
						});

						// Remove Split testing related data
						view.model.unset( 'et_pb_ab_subject' );
						view.model.unset( 'et_pb_ab_subject_id' );

						// Auto expand collapsed winner
						if ( view.model.has( 'et_pb_collapsed' ) && view.model.get( 'et_pb_collapsed' ) === 'on' ) {
							view.model.unset( 'et_pb_collapsed' );
						}

						// Return all disguised global item into actual global item
						if ( ET_PageBuilder_Layout.is_temp_global( view.model ) || ET_PageBuilder_Layout.is_temp_global_children( view.model ) ) {

							et_pb_create_prompt_modal( 'set_global_subject_winner', undefined, undefined, undefined, { view : view } );

							return;
						}

						// Turn off Split testing sequence
						this.turn_off_ab_testing_sequence();

						return;
					} else {
						// Prompt modal box to select winner
						ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_winner_first' );
						return;
					}
				}
			},

			turn_off_ab_testing_sequence : function () {
				// Reset subjects_rank data
				et_pb_ab_js_options.subjects_rank = {};

				// Save as shortcode & rebuild builder
				et_reinitialize_builder_layout();

				// Wait until et_reinitialize_builder_layout() sequences have been finished
				// to disable is_doing_combination status
				setTimeout( function() {
					ET_PageBuilder_App.is_doing_combination = false;

					ET_PageBuilder_App.allowHistorySaving( 'turnoff', 'abtesting' );

					delete ET_PageBuilder_App.disable_publish;
					$( '#publish' ).removeClass( 'disabled' );
				}, 650 );

				// Remove nescesarry class for Split testing winner selection mode's UI
				$( '#et_pb_layout' ).removeClass( 'et_pb_select_ab_testing_winner' );

				// Hide view stats icon
				$( '#et_pb_layout .et-pb-app-view-ab-stats-button' ).removeClass( 'active' );

				// Re-enable portability
				this.toggle_portability( true );

				// clear stats for disabled test
				$.ajax({
					type : "POST",
					url : et_pb_options.ajaxurl,
					data : {
						action : 'et_pb_ab_clear_stats',
						et_pb_ab_nonce : et_pb_options.ab_testing_builder_nonce,
						et_pb_test_id : et_pb_ab_js_options.test_id
					},
					success : function( response ) {
						// Reset report data
						et_pb_ab_js_options.has_report = false;
						ET_PageBuilder_App.ab_stats = {};
					}
				});
			},

			update_saved_subject_ids : function () {
				var subject_ids = this.subject_ids(),
					formatted_subject_ids = subject_ids.join();

				if ( $( '#et_pb_ab_subjects' ).val() !== formatted_subject_ids ) {
					$( '#et_pb_ab_subjects' ).val( formatted_subject_ids ).addClass('et_pb_value_updated');
				}

				set_this_editor_in_use();
			},

			update_layout : function () {
				if ( this.is_active() ) {
					/* Layout adjustment */
					setTimeout( function() {
						// Add disable subject removal class
						var $et_pb_layout = $( '#et_pb_layout' );

						if ( ET_PageBuilder_AB_Testing.count_subjects() < 3 ) {
							$et_pb_layout.addClass( 'et_pb_ab_disable_subject_removal' );
						} else {
							$et_pb_layout.removeClass( 'et_pb_ab_disable_subject_removal' );
						}
					}, 100 );

					/* Individual subject adjustment */

					// Section
					if ( $( '.et_pb_section.et_pb_ab_subject' ).length ) {
						$( '.et_pb_ab_subject_first' ).removeClass( 'et_pb_ab_subject_first' );
						$( '.et_pb_ab_subject_last' ).removeClass( 'et_pb_ab_subject_last' );

						$( '.et_pb_section' ).each(function(){
							var $section = $(this);

							$section.find( '.et_pb_section.et_pb_ab_subject:first' ).addClass( 'et_pb_ab_subject_first' );
							$section.find( '.et_pb_section.et_pb_ab_subject:last' ).addClass( 'et_pb_ab_subject_last' );
						});

						// Loop sections, adjust section class
						$( '.et_pb_section.et_pb_ab_subject' ).each(function() {
							var $section = $(this);

							if ( ! $section.prev().hasClass( 'et_pb_ab_subject' ) ) {
								$section.addClass( 'et_pb_ab_subject_first' );
							}

							if ( ! $section.next().hasClass( 'et_pb_ab_subject' ) ) {
								$section.addClass( 'et_pb_ab_subject_last' );
							}
						});
					}

					// Row
					if ( $( '.et_pb_row.et_pb_ab_subject' ).length ) {
						$( '.et_pb_ab_subject_first' ).removeClass( 'et_pb_ab_subject_first' );
						$( '.et_pb_ab_subject_last' ).removeClass( 'et_pb_ab_subject_last' );

						$( '.et_pb_section' ).each(function(){
							var $section = $(this);

							$section.find( '.et_pb_row.et_pb_ab_subject:first' ).addClass( 'et_pb_ab_subject_first' );
							$section.find( '.et_pb_row.et_pb_ab_subject:last' ).addClass( 'et_pb_ab_subject_last' );
						});

						// Loop rows, adjust row class
						$( '.et_pb_row.et_pb_ab_subject' ).each(function() {
							var $row = $(this);

							if ( ! $row.prev().hasClass( 'et_pb_ab_subject' ) ) {
								$row.addClass( 'et_pb_ab_subject_first' );
							}

							if ( ! $row.next().hasClass( 'et_pb_ab_subject' ) ) {
								$row.addClass( 'et_pb_ab_subject_last' );
							}
						});
					}

					if ( $( '.et_pb_row.et_pb_ab_subject.et_pb_ab_no_permission' ).length || $( '.et_pb_row.et_pb_ab_goal.et_pb_ab_no_permission' ).length ) {
						$( '.et_pb_row.et_pb_ab_subject.et_pb_ab_no_permission, .et_pb_row.et_pb_ab_goal.et_pb_ab_no_permission' ).each( function() {
							var $row = $(this);

							$row.closest( '.et_pb_section' ).addClass( 'et_pb_ab_no_permission_parent' );
						});
					}

					// Module
					if ( $( '.et_pb_module_block.et_pb_ab_subject' ).length ) {
						$( '.et_pb_ab_subject_first' ).removeClass( 'et_pb_ab_subject_first' );
						$( '.et_pb_ab_subject_last' ).removeClass( 'et_pb_ab_subject_last' );

						// Loop columns, adjust module class
						$( '.et-pb-column' ).each(function(){
							var $column = $(this);

							$column.find( '.et_pb_module_block.et_pb_ab_subject:first' ).addClass( 'et_pb_ab_subject_first' );
							$column.find( '.et_pb_module_block.et_pb_ab_subject:last' ).addClass( 'et_pb_ab_subject_last' );
						});

						// Loop subjects, adjust module class
						$( '.et_pb_module_block.et_pb_ab_subject' ).each(function(){
							var $module = $(this);

							if ( ! $module.prev().hasClass( 'et_pb_ab_subject' ) ) {
								$module.addClass( 'et_pb_ab_subject_first' );
							}

							if ( ! $module.next().hasClass( 'et_pb_ab_subject' ) ) {
								$module.addClass( 'et_pb_ab_subject_last' );
							}
						});
					}


					if ( $( '.et_pb_module_block.et_pb_ab_subject.et_pb_ab_no_permission' ).length || $( '.et_pb_module_block.et_pb_ab_goal.et_pb_ab_no_permission' ).length ) {
						$( '.et_pb_module_block.et_pb_ab_subject.et_pb_ab_no_permission, .et_pb_module_block.et_pb_ab_goal.et_pb_ab_no_permission' ).each( function() {
							var $module_block = $(this);

							$module_block.closest( '.et_pb_row' ).addClass( 'et_pb_ab_no_permission_parent' );
							$module_block.closest( '.et_pb_section' ).addClass( 'et_pb_ab_no_permission_parent' );
						});
					}

					// Make sure that there is at least one expanded subject for carousel effect
					var expand_first_subject = true,
						$first_subject,
						first_subject_cid,
						first_subject_view;

					_.each( this.subjects(), function( subject ){
						if ( ! subject.has( 'et_pb_collapsed' ) || subject.get( 'et_pb_collapsed' ) === 'off' ) {
							expand_first_subject = false;
						}
					});

					if ( expand_first_subject ) {
						$first_subject = $('.et_pb_ab_subject:first');
						first_subject_cid = $first_subject.children( '.et-pb-data-cid' ).attr( 'data-cid' );
						first_subject_view = ET_PageBuilder_Layout.getView( first_subject_cid );

						if ( $first_subject.length && ! _.isUndefined( first_subject_view ) ) {
							$first_subject.removeClass( 'et_pb_collapsed' );

							first_subject_view.model.set( 'et_pb_collapsed', 'off' );
						}
					}
				}
			},

			update : function () {
				this.update_saved_subject_ids();
				this.update_layout();
			},

			is_alert_valid : function( alert_id ) {
				// Prevent similar alert to be toggled twice
				if ( ! _.isUndefined( ET_PageBuilder_App.ab_last_visible_alert ) && ET_PageBuilder_App.ab_last_visible_alert === alert_id ) {
					return false;
				}

				// Log last visible alert
				ET_PageBuilder_App.ab_last_visible_alert = alert_id;

				return true;
			},

			alert : function( alert_id ) {
				// Prevent similar alert to be toggled twice
				if ( ! this.is_alert_valid( alert_id ) ) {
					return;
				}

				// Display alert
				et_pb_create_prompt_modal( 'ab_testing_alert', undefined, undefined, undefined, { id : alert_id } );
			},

			alert_yes_no : function( alert_id ) {
				// Prevent similar alert to be toggled twice
				if ( ! this.is_alert_valid( alert_id ) ) {
					return;
				}

				// Display alert
				et_pb_create_prompt_modal( 'ab_testing_alert_yes_no', undefined, undefined, undefined, { id : alert_id } );
			},

			get_all_subjects_stats_settings : function( analysis ) {
				var $tab              = $( '.view-stats-tab[data-analysis="' + analysis + '"]' ),
					$subjects_filter  = $tab.find( '.et-pb-ab-view-stats-subjects-filter' ),
					$durations_filter = $tab.find( '.et-pb-ab-view-stats-time-filter' ),
					$analysis_tab_nav = $tab.find( '.et-pb-options-tabs-links' ),
					table_column_key  = ['first', 'second', 'third', 'fourth', 'fifth'],
					settings = {
						subject_statuses : [],
						subject_ids : [],
						table : {
							thead : [],
							tbody : {},
							tfoot : []
						}
					},
					data;

				if ( ! $durations_filter.find( '.active' ).length ) {
					$durations_filter.find('a[data-duration="' + et_pb_ab_js_options.refresh_interval_duration + '"]').addClass('active');
				}

				data = ET_PageBuilder_App.ab_stats[ $durations_filter.find( '.active' ).attr( 'data-duration' ) ];

				// Get active subjects
				$subjects_filter.find( 'a' ).each(function(){
					var $filter = $(this),
						subject_status = ! $filter.hasClass( 'inactive' ),
						subject_has_data = ! $filter.parent( 'li' ).hasClass( 'et-pb-no-data' ),
						subject_id = $filter.attr( 'data-subject-id' );

					if ( subject_has_data ) {
						settings.subject_statuses.push( subject_status );
					}

					if ( subject_status && subject_has_data ) {
						settings.subject_ids.push( parseInt( subject_id ) );
					}
				});

				// Push table's heading data
				for ( var thead_tr_index = 0; thead_tr_index < 5; thead_tr_index++ ) {
					settings.table.thead[ table_column_key[ thead_tr_index ] ] = et_pb_ab_js_options['view_stats_thead_titles'][ analysis ][ thead_tr_index ];
				}

				// Push table's body data
				if ( ! _.isUndefined( data ) ) {
					_.each( data.subjects_id, function( subject_id ) {
						var subject_key = 'subject_' + subject_id,
							subject_model = ET_PageBuilder_Modules.findWhere({ et_pb_ab_subject_id : subject_id }),
							subject_name  = _.isUndefined( subject_model ) || ! subject_model.has( 'admin_label' ) ? false : subject_model.get( 'admin_label' );

						if ( ! subject_name || $.inArray( parseInt( subject_id ), settings.subject_ids ) === -1 ) {
							return;
						}

						settings.table.tbody[ subject_key ] = {
							first : subject_id,
							second : subject_name,
							third : data['subjects_totals'][ subject_key ][ et_pb_ab_js_options['analysis_formula'][ analysis ]['denominator'] ],
							fourth : data['subjects_totals'][ subject_key ][ et_pb_ab_js_options['analysis_formula'][ analysis ]['numerator'] ],
							fifth : data['subjects_totals'][ subject_key ][ analysis ]+ '%'
						};
					});

					settings.table.tfoot = {
						first : et_pb_ab_js_options.total_title,
						second : null,
						third : data['events_totals'][ et_pb_ab_js_options['analysis_formula'][ analysis ]['denominator'] ],
						fourth : data['events_totals'][ et_pb_ab_js_options['analysis_formula'][ analysis ]['numerator'] ],
						fifth : data['events_totals'][ analysis ] + '%'
					};
				}

				return settings;
			},

			switch_view_stats_tab : function() {
				var $tab_nav = $( '.et-pb-options-tabs-links' ),
					$tabs_wrapper = $( '.et-pb-ab-view-stats-content.has-data' ),
					active_analysis = $tab_nav.find( 'li.et-pb-options-tabs-links-active' ).attr( 'data-analysis' );

				$tabs_wrapper.find( '.view-stats-tab' ).removeClass( 'active' );
				$tabs_wrapper.find( '.view-stats-tab[data-analysis="' + active_analysis + '"]' ).addClass( 'active' );
			},

			display_stats_tabs : function( data ) {
				var this_el                 = this,
					thead_template          = _.template("<tr><th><%= first %></th><th><%= second %></th><th><%= third %></th><th><%= fourth %></th><th><%= fifth %></th></tr>"),
					tbody_row_template      = _.template("<tr><td><%= first %></td><td><%= second %></td><td><%= third %></td><td><%= fourth %></td><td><%= fifth %></td></tr>"),
					tfoot_template          = _.template("<tr><td colspan='2'><%= first %></td></td><td><%= third %></td><td><%= fourth %></td><td><%= fifth %></td></tr>"),
					$prompt_modal = $( '.et_pb_prompt_modal.et_pb_ab_view_stats' ),
					goal_model    = ET_PageBuilder_Modules.findWhere({ et_pb_ab_goal : "on"}),
					goal_module_type = goal_model.get( 'module_type' ),
					subject_ids = 	$('#et_pb_ab_subjects').val().split(",");

				if ( ! _.isEmpty( data.subjects_totals ) || et_pb_ab_js_options.has_report ) {

					// Hide conversions tabs, adjust conversion copy
					if ( $.inArray( goal_module_type, et_pb_ab_js_options.have_conversions  ) === -1 ) {
						$( '.et_pb_options_tab_ab_stat_conversion, .view-stats-tab.tab-conversions' ).remove();
						$( '.et_pb_options_tab_ab_stat_clicks' ).addClass( 'et-pb-options-tabs-links-active' );
					} else if ( goal_module_type === 'et_pb_shop' ) {
						$( '.et_pb_options_tab_ab_stat_conversion a' ).text( et_pb_ab_js_options.sales_title );
					}

					// remove the shortcode tracking tab if this option disabled
					if ( 'on' !== ET_PageBuilder_AB_Testing.get_shortcode_tracking_status() ) {
						$( '.et_pb_options_tab_ab_stat_shortcode_conversions' ).remove();
					}

					// Initiate on each tab
					$prompt_modal.find( '.view-stats-tab' ).each(function() {
						var $tab     = $(this),
							analysis = $tab.attr( 'data-analysis' ),
							$line_chart          = $( '#ab-testing-stats-' + analysis ),
							$pie_chart           = $( '#ab-testing-stats-pie-' + analysis ),
							$ab_testing_table       = $( '#view-stats-table-' + analysis ),
							$ab_testing_table_thead = $ab_testing_table.find( 'thead' ),
							$ab_testing_table_tbody = $ab_testing_table.find( 'tbody' ),
							$ab_testing_table_tfoot = $ab_testing_table.find( 'tfoot' ),
							$subjects_filter        = $tab.find( '.et-pb-ab-view-stats-subjects-filter' ),
							$durations_filter       = $tab.find( '.et-pb-ab-view-stats-time-filter' ),
							$pie_chart_legends      = $tab.find( '.ab-testing-stats-pie-legends' ),
							$analysis_tab_nav       = $( '.et_pb_ab_view_stats .et-pb-options-tabs-links' ),
							line_chart_data = {
								labels: data.dates,
								datasets: []
							},
							pie_chart_data = [],
							line_chart,
							line_chart_datasets,
							pie_chart,
							pie_chart_segments,
							stats_settings;

						// Append line and pie graph legends
						_.each( subject_ids, function( subject_id ){
							var subject_key   = 'subject_' + subject_id,
								subject_model = ET_PageBuilder_Modules.findWhere({ et_pb_ab_subject_id : subject_id }),
								subject_name  = _.isUndefined( subject_model ) || ! subject_model.has( 'admin_label' ) ? false : subject_model.get( 'admin_label' );

							if ( ! subject_name ) {
								return;
							}

							// Generate subject toggle button
							$toggle_subject = $( '<li />' ).append( $( '<a />', { href : '#', 'data-subject-id' : subject_id } ).text( subject_name ) );
							$legend_subject = $( '<li />', { 'data-subject-id' : subject_id  } ).append( $( '<a />', { href : '#' } ).text( subject_name ) ).prepend( $( '<span />' ) );

							$subjects_filter.append( $toggle_subject );
							$pie_chart_legends.append( $legend_subject );
						});

						// Loop each subject data and insert needed UI
						if ( ! _.isEmpty( data ) && ! _.isUndefined( data ) && data ) {

							// Draw graph
							draw_graphs = this_el.draw_graphs(
								analysis,
								data,
								line_chart,
								pie_chart,
								$line_chart,
								$pie_chart,
								$ab_testing_table,
								$ab_testing_table_thead,
								$ab_testing_table_tbody,
								$ab_testing_table_tfoot,
								thead_template,
								tbody_row_template,
								tfoot_template,
								true
							);

							// Update tab's variables
							line_chart          = draw_graphs.line_chart;
							pie_chart           = draw_graphs.pie_chart;
							line_chart_data     = draw_graphs.line_chart_data;
							pie_chart_data      = draw_graphs.pie_chart_data;
							line_chart_datasets = draw_graphs.line_chart_datasets;
							pie_chart_segments  = draw_graphs.pie_chart_segments;
						} else {
							var $active_durations_filter = $durations_filter.find( 'a[data-duration="' + et_pb_ab_js_options.refresh_interval_duration + '"]' );

							$active_durations_filter.addClass( 'active' );

							$tab.addClass( 'no-tab-data' );
						}

						// Toggle subject stats and graph
						$subjects_filter.on( 'click', 'a', function(e) {
							e.preventDefault();

							var $link = $(this),
								subject_id = $link.attr( 'data-subject-id' );

							// Toggle subject nav
							$(this).toggleClass( 'inactive' );

							$pie_chart_legends.find( 'li[data-subject-id="'+ subject_id +'"]' ).toggleClass( 'inactive' );

							// Filter by subject
							filter_subject = this_el.filter_stats_subject(
								analysis,
								line_chart,
								pie_chart,
								line_chart_datasets,
								pie_chart_segments,
								$ab_testing_table,
								$ab_testing_table_thead,
								$ab_testing_table_tbody,
								$ab_testing_table_tfoot,
								thead_template,
								tbody_row_template,
								tfoot_template
							);

							line_chart = filter_subject.line_chart;
							pie_chart = filter_subject.pie_chart;
						});

						// Toggle legend
						$pie_chart_legends.on( 'click', 'a', function(e) {
							e.preventDefault();

							var $link = $(this),
								li = $link.parent( 'li' ),
								subject_id = li.attr( 'data-subject-id' );

							$subjects_filter.find( 'a[data-subject-id="' + subject_id + '"]' ).trigger( 'click' );
						});

						// Switch between tabs
						$analysis_tab_nav.on( 'click', 'a', function(e) {
							e.preventDefault();

							// skip the refresh button
							if ( $( this ).hasClass( 'et-pb-ab-refresh-stats' ) ) {
								return;
							}

							$analysis_tab_nav.find( 'li' ).removeClass( 'et-pb-options-tabs-links-active' );

							$(this).parent( 'li' ).addClass( 'et-pb-options-tabs-links-active' );

							this_el.switch_view_stats_tab();
						});

						// Change time context of the graph
						$durations_filter.on( 'click', 'a', function(e) {
							e.preventDefault();

							var $filter        = $(this),
								duration       = $filter.attr( 'data-duration' ),
								$prompt_modal  = $( '.et_pb_prompt_modal.et_pb_ab_view_stats' );

							$durations_filter.find( 'a' ).removeClass( 'active' );

							$filter.addClass( 'active' );

							if ( _.isUndefined( ET_PageBuilder_App.ab_stats[ duration ] ) || ! ET_PageBuilder_App.ab_stats[ duration ] ) {
								// Start loading bar
								ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

								// Get Split statistics data
								$.ajax({
									type : "POST",
									url : et_pb_options.ajaxurl,
									data : {
										action : 'et_pb_ab_builder_data',
										et_pb_ab_nonce : et_pb_options.ab_testing_builder_nonce,
										et_pb_ab_test_id : et_pb_ab_js_options.test_id,
										et_pb_ab_duration : duration
									},
									success : function( data ) {
										// Remove no tab data state
										$tab.removeClass( 'no-tab-data' );

										// Stop loading bar
										ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

										if ( 'false' === data ) {
											// Display no-data notification
											$tab.addClass( 'no-tab-data' );
											return;
										}

										data = $.parseJSON( data );

										// Save graph data for quick retrieval if there are data found
										ET_PageBuilder_App.ab_stats[ duration ] = data;

										// Update graph
										draw_graphs = this_el.draw_graphs(
											analysis,
											data,
											line_chart,
											pie_chart,
											$line_chart,
											$pie_chart,
											$ab_testing_table,
											$ab_testing_table_thead,
											$ab_testing_table_tbody,
											$ab_testing_table_tfoot,
											thead_template,
											tbody_row_template,
											tfoot_template
										);

										// Update tab's variables
										line_chart          = draw_graphs.line_chart;
										pie_chart           = draw_graphs.pie_chart;
										line_chart_data     = draw_graphs.line_chart_data;
										pie_chart_data      = draw_graphs.pie_chart_data;
										line_chart_datasets = draw_graphs.line_chart_datasets;
										pie_chart_segments  = draw_graphs.pie_chart_segments;
									}
								});
							} else {
								// Remove no tab data state
								$tab.removeClass( 'no-tab-data' );

								// Get appropriate data based on duration
								data = ET_PageBuilder_App.ab_stats[ duration ];

								// Update graph
								draw_graphs = this_el.draw_graphs(
									analysis,
									data,
									line_chart,
									pie_chart,
									$line_chart,
									$pie_chart,
									$ab_testing_table,
									$ab_testing_table_thead,
									$ab_testing_table_tbody,
									$ab_testing_table_tfoot,
									thead_template,
									tbody_row_template,
									tfoot_template
								);

								// Update tab's variables
								line_chart          = draw_graphs.line_chart;
								pie_chart           = draw_graphs.pie_chart;
								line_chart_data     = draw_graphs.line_chart_data;
								pie_chart_data      = draw_graphs.pie_chart_data;
								line_chart_datasets = draw_graphs.line_chart_datasets;
								pie_chart_segments  = draw_graphs.pie_chart_segments;
							}
						});
					})

					// Refresh split test
					$prompt_modal.on( 'click', '.et-pb-ab-refresh-stats', function() {
						ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

						$.ajax({
							type : "POST",
							url : et_pb_options.ajaxurl,
							data : {
								action : 'et_pb_ab_clear_cache',
								et_pb_ab_nonce : et_pb_options.ab_testing_builder_nonce,
								et_pb_test_id : et_pb_ab_js_options.test_id
							},
							success : function( data ) {
								ET_PageBuilder_App.ab_stats = {};

								// emulate click on the current filter link to re-draw the current graph
								$( '.et-pb-ab-view-stats-time-filter' ).find( 'a.active' ).click();
							}
						});
					} );

					// Display UI
					$prompt_modal.addClass( 'et-pb-loaded' ).find( '.et-pb-ab-view-stats-content.has-data, .et-pb-options-tabs-links' ).css({ 'opacity' : 1 });

					this_el.switch_view_stats_tab();

				} else {
					// Display no-data notification
					$prompt_modal.find( '.et-pb-ab-view-stats-content.has-data, .et-pb-options-tabs-links' ).hide();
					$prompt_modal.find( '.et-pb-ab-view-stats-content.no-data' ).css({ 'opacity' : 1, 'display' : 'block' });
					return;
				}
			},

			draw_graphs : function( analysis, data, line_chart, pie_chart, $line_chart, $pie_chart, $ab_testing_table, $ab_testing_table_thead, $ab_testing_table_tbody, $ab_testing_table_tfoot, thead_template, tbody_row_template, tfoot_template, skip_subject_filtering  ) {
				var line_chart_data = {
						labels: $.merge( [""], data.dates ),
						datasets: []
					},
					pie_chart_data = [],
					$tab = $line_chart.closest( '.view-stats-tab' ),
					$toggle_subject = $tab.find( '.et-pb-ab-view-stats-subjects-filter' ),
					$legend_subject = $tab.find( '.ab-testing-stats-pie-legends' );

				_.each( data.subjects_id, function( subject_id ){
					var subject_key   = 'subject_' + subject_id,
						subject_model = ET_PageBuilder_Modules.findWhere({ et_pb_ab_subject_id : subject_id }),
						subject_name  = _.isUndefined( subject_model ) || _.isUndefined( subject_model.attributes.admin_label ) ? false : subject_model.attributes.admin_label,
						$toggle_subject_item = $toggle_subject.find( 'a[data-subject-id="' + subject_id + '"]' ),
						$legend_subject_item = $legend_subject.find( 'li[data-subject-id="' + subject_id + '"] span' );

					if ( ! subject_name ) {
						return;
					}

					// Update toggle and legend item if needed
					if ( _.isUndefined( $toggle_subject_item.attr( 'style' ) ) ) {
						$toggle_subject_item.css({ 'backgroundColor' : data['subjects_totals'][ subject_key ]['color'] } );
					}

					if ( _.isUndefined( $legend_subject_item.attr( 'style' ) ) ) {
						$legend_subject_item.css({ 'backgroundColor' : data['subjects_totals'][ subject_key ]['color'] } );
					}

					// Generate line chart data
					line_chart_data.datasets.push({
						subject_id : subject_id,
						label: subject_name,
						fillColor: "transparent",
						strokeColor: data['subjects_totals'][ subject_key ]['color'],
						pointColor: data['subjects_totals'][ subject_key ]['color'],
						pointStrokeColor: "#fff",
						data: $.merge( [ null ], _.values( data['subjects_analysis'][ subject_key ][ analysis ] ) )
					});

					// Generate pie chart data
					pie_chart_data.push({
						value : ( data['subjects_totals'][ subject_key ][ analysis ] ),
						color : data['subjects_totals'][ subject_key ]['color'],
						label : '#' + subject_id + ': ' + subject_name
					});
				});

				// Reset hide/show state of toggle subject and pie chart legend
				$toggle_subject.find('li').removeClass( 'et-pb-no-data' );
				$legend_subject.find('li').removeClass( 'et-pb-no-data' );

				// Hide/show toggle and legend
				_.each( $( '#et_pb_ab_subjects' ).val().split( ',' ), function( subject_id ) {
					if ( $.inArray( subject_id, data.subjects_id ) === -1 ) {
						$toggle_subject.find( 'a[data-subject-id="' + subject_id + '"]' ).parent( 'li' ).addClass( 'et-pb-no-data' );
						$legend_subject.find( 'li[data-subject-id="' + subject_id + '"]' ).addClass( 'et-pb-no-data' );
					}
				} )

				// Define stats settings
				stats_settings = this.get_all_subjects_stats_settings( analysis );

				// Draw Table
				$ab_testing_table_thead.empty().html( $( thead_template( stats_settings.table.thead ) ) );

				$ab_testing_table_tbody.empty();

				_.each( stats_settings.table.tbody, function( row ){
					$ab_testing_table_tbody.append( $( tbody_row_template( row ) ) );
				});

				$ab_testing_table_tfoot.empty().html( $( tfoot_template( stats_settings.table.tfoot ) ) );

				if ( _.size( stats_settings.table.tbody ) > 1 ) {
					// Make table sortable
					$ab_testing_table.tablesorter();

					var $ab_testing_table_first_head_column = $ab_testing_table.find( 'thead th:first' );

					if ( ! $ab_testing_table_first_head_column.hasClass( '.headerSortDown' ) ) {
						setTimeout( function(){
							$ab_testing_table_first_head_column.trigger( 'click' );
						}, 500 );
					}
				}

				// Draw Line Chart
				if ( ! _.isUndefined( line_chart ) ) {
					line_chart.destroy();
				}

				// make the tab's content visible to draw the graphs
				$line_chart.closest( '.view-stats-tab' ).addClass( 'et_pb_ab_visible_tab' );

				line_chart = new Chart( $line_chart.get(0).getContext("2d") ).Line(
					line_chart_data,
					{
						scaleFontSize : 13,
						scaleFontColor : "#a1a9b1",
						scaleLabel: "<%=value%>%",
						scaleGridLineWidth : 2,
						scaleLineWidth: 2,
						tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>%",
						multiTooltipTemplate: "<%= value %>%",
						datasetStrokeWidth : 4,
						pointDotStrokeWidth : 2,
						pointDotRadius : 7
					}
				);

				// Save datasets
				line_chart_datasets = line_chart.datasets;

				// Draw Pie chart
				if ( ! _.isUndefined( pie_chart ) ) {
					pie_chart.destroy();
				}

				// initialize the pie chart only if canvas visible to avoid js errors
				if ( $pie_chart.is( ':visible' ) ) {
					pie_chart = new Chart( $pie_chart.get(0).getContext("2d") ).Pie(
						pie_chart_data,
						{
							animationEasing : 'easeInCubic',
							animationSteps : 50,
							tooltipTemplate: "<%if (label){%><%=label%><%}%>"
						}
					);

					// Save segments
					pie_chart_segments = pie_chart.segments;
				}

				// Filter by subject
				if ( _.isUndefined( skip_subject_filtering ) ) {
					filter_subject = this.filter_stats_subject(
						analysis,
						line_chart,
						pie_chart,
						line_chart_datasets,
						pie_chart_segments,
						$ab_testing_table,
						$ab_testing_table_thead,
						$ab_testing_table_tbody,
						$ab_testing_table_tfoot,
						thead_template,
						tbody_row_template,
						tfoot_template
					);
					line_chart = filter_subject.line_chart;
					pie_chart  = filter_subject.pie_chart;
				}

				// remove visible class after graphs were generated
				$line_chart.closest( '.view-stats-tab' ).removeClass( 'et_pb_ab_visible_tab' );

				return {
					line_chart : line_chart,
					pie_chart : pie_chart,
					line_chart_data : line_chart_data,
					pie_chart_data : pie_chart_data,
					line_chart_datasets : line_chart_datasets,
					pie_chart_segments : pie_chart_segments
				}
			},

			filter_stats_subject : function( analysis, line_chart, pie_chart, line_chart_datasets, pie_chart_segments, $ab_testing_table, $ab_testing_table_thead, $ab_testing_table_tbody, $ab_testing_table_tfoot, thead_template, tbody_row_template, tfoot_template ) {
				// Get settings and modify chart data
				var stats_settings          = this.get_all_subjects_stats_settings( analysis ),
					updated_line_chart_data = _.compact( _.map( line_chart_datasets, function( item, key ) { return stats_settings.subject_statuses[ key ] ? item : false } ) ),
					updated_pie_chart_data  = _.compact( _.map( pie_chart_segments, function( item, key ) { return stats_settings.subject_statuses[ key ] ? item : false } ) ),
					table_row_size;

				// Update line chart
				line_chart.datasets = updated_line_chart_data;
				line_chart.update();

				// Emptying table
				$ab_testing_table_thead.empty();
				$ab_testing_table_tbody.empty();
				$ab_testing_table_tfoot.empty();

				// Update table's head
				$ab_testing_table_thead.html( $( thead_template( stats_settings.table.thead ) ) );

				_.each( stats_settings.table.tbody, function( row ){
					$ab_testing_table_tbody.append( $( tbody_row_template( row ) ) );
				});

				// Update table row size
				table_row_size = _.size( stats_settings.table.tbody );

				// Initiate table sorter and manually sorting table by triggering click if there is a body table
				if ( table_row_size > 1 ) {
					// Make table sortable
					$ab_testing_table.tablesorter();

					var $ab_testing_table_first_head_column = $ab_testing_table.find( 'thead th:first' );

					if ( ! $ab_testing_table_first_head_column.hasClass( '.headerSortDown' ) ) {
						setTimeout( function(){
							$ab_testing_table_first_head_column.trigger( 'click' );
						}, 500 );
					}
				}

				// Update table footer calculation
				if ( table_row_size > 0 ) {
					tfoot_third_data  = _.pluck( stats_settings.table.tbody, 'third' );
					tfoot_fourth_data = _.pluck( stats_settings.table.tbody, 'fourth' );
					tfoot_fifth_data  = _.map( _.pluck( stats_settings.table.tbody, 'fifth' ), function( num ) { return parseFloat( num ) });
					tfoot_third       = _.reduce( tfoot_third_data, function( a, b ) { return a + b; }, 0 );
					tfoot_fourth      = _.reduce( tfoot_fourth_data, function( a, b ) { return a + b; }, 0 );
					tfoot_fifth       = ( _.reduce( tfoot_fifth_data, function( a, b ) { return a + b; }, 0 ) / _.size( tfoot_fifth_data ) ).toFixed(2) + '%';

					$ab_testing_table_tfoot.html( $( tfoot_template( {
						first : et_pb_ab_js_options.total_title,
						second : null,
						third : tfoot_third,
						fourth : tfoot_fourth,
						fifth : tfoot_fifth
					} ) ) );
				}

				// Update pie chart
				pie_chart.segments = updated_pie_chart_data;
				pie_chart.update();

				return {
					line_chart : line_chart,
					pie_chart : pie_chart
				}
			},

			delete_post_meta : function(){
				// Update all Split testing related hidden post meta data so it will be removed when the page is updated
				this.toggle_status( false );
				$( '#et_pb_ab_subjects' ).val( '' );

				set_this_editor_in_use();
			},

			subject_carousel : function( cid ) {
				var current_subject_view = ET_PageBuilder_Layout.getView( cid ),
					subject_models       = ET_PageBuilder_Modules.where({ 'et_pb_ab_subject' : 'on' }),
					collapse_status      = current_subject_view.model.get( 'et_pb_collapsed' ),
					collapse_status_others = collapse_status === 'on' ? 'off' : 'on';

				_.each( subject_models, function( subject_model ){
					var subject_view = ET_PageBuilder_Layout.getView( subject_model.attributes.cid );

					if ( subject_view.model.get( 'cid' ) === current_subject_view.model.get( 'cid' ) ) {
						if ( collapse_status === 'on' ) {
							subject_view.model.set( 'et_pb_collapsed', 'off' );
						}
					} else {
						if ( collapse_status === 'on'  ) {
							subject_view.model.set( 'et_pb_collapsed', 'on' );
						} else {
							subject_view.model.set( 'et_pb_collapsed', collapse_status_others );
						}
					}

					if ( subject_view.model.get( 'et_pb_collapsed' ) === 'on' ) {
						subject_view.$el.addClass( 'et_pb_collapsed' );
					} else {
						subject_view.$el.removeClass( 'et_pb_collapsed' );
					}
				});
			}
		};

		$et_pb_content.remove();

		// button can be disabled, therefore use the button wrapper to determine whether to display builder or not
		if ( $toggle_builder_button_wrapper.hasClass( 'et_pb_builder_is_used' ) ) {
			$builder.show();

			et_pb_hide_layout_settings();
		}

		$toggle_builder_button.click( function( event ) {
			event.preventDefault();

			var $this_el = $(this),
				is_builder_used = $this_el.hasClass( 'et_pb_builder_is_used' ),
				$et_pb_fb_cta = $( '#et_pb_fb_cta' ),
				content;

			if ( is_builder_used ) {
				et_pb_create_prompt_modal( 'deactivate_builder' );
			} else {
				content = et_pb_get_content( 'content' );

				$et_pb_old_content.val( content );

				ET_PageBuilder_App.reInitialize();

				$use_builder_custom_field.val( 'on' );

				$builder.show();

				$this_el.text( $this_el.data( 'editor' ) );

				$main_editor_wrapper.toggleClass( 'et_pb_hidden' );

				$this_el.toggleClass( 'et_pb_builder_is_used' );

				ET_PageBuilder_Events.trigger( 'et-activate-builder' );

				et_pb_hide_layout_settings();

				$et_pb_fb_cta.show();
			}
		} );

		$( '#et_pb_fb_cta' ).click( function( e ) {
			e.preventDefault();

			// Fade wrap.
			$( 'html' ).fadeOut();

			// Set redirect.
			$( 'form#post' ).append( '<input type="hidden" name="et-fb-builder-redirect" value="' + $( this ).attr('href') + '" />' );

			// Set builder.
			if ( ! $toggle_builder_button.hasClass( 'et_pb_builder_is_used' ) ) {
				$et_pb_old_content.val( et_pb_get_content( 'content' ) );

				ET_PageBuilder_App.reInitialize();

				$use_builder_custom_field.val( 'on' );
			}

			// Prevent reload box and queue save to allow builder to create empty section if necessary.
			$( window )
				.off( 'beforeunload' )
				.delay( 500 )
				.queue( function() {
					var trigger = $( '#save-action #save-post' );

					// Publish if save draft isn't present.
					if ( trigger.length === 0 ){
						trigger = $( '#publishing-action #publish' );
					}

					trigger.trigger( 'click' );
				} );
		} ).contextmenu( function() {
			$.ajax( {
				type: 'POST',
				url: $( '#post' ).attr( 'action' ),
				data: $('#post').serializeArray(),
			} );
		} );

		function et_pb_deactivate_builder() {
			var $body = $( 'body' ),
				page_position = 0;
				$et_pb_fb_cta = $( '#et_pb_fb_cta' );

			$et_pb_fb_cta.hide();

			et_pb_set_content( 'content', $et_pb_old_content.val() );

			window.wpActiveEditor = 'content';

			$use_builder_custom_field.val( 'off' );

			$builder.hide();

			$toggle_builder_button.text( $toggle_builder_button.data( 'builder' ) ).toggleClass( 'et_pb_builder_is_used' );

			$main_editor_wrapper.toggleClass( 'et_pb_hidden' );

			et_pb_show_layout_settings();

			page_position = $body.scrollTop();

			$body.scrollTop( page_position + 1 );

			ET_PageBuilder_Events.trigger( 'et-deactivate-builder' );

			// If Split testing is active, remove all Split testing related post meta
			if ( ET_PageBuilder_AB_Testing.is_active() ) {
				ET_PageBuilder_AB_Testing.delete_post_meta();
			}

			//trigger window resize event to trigger tinyMCE editor toolbar sizes recalculation.
			$( window ).trigger( 'resize' );
		}

		function et_pb_create_prompt_modal( action, cid_or_element, module_width, columns_layout, template_settings ) {
			var	on_top_class = -1 !== $.inArray( action, [ 'save_template', 'reset_advanced_settings' ] ) ? ' et_modal_on_top' : '',
				on_top_both_actions_class = 'reset_advanced_settings' === action ? ' et_modal_on_top_both_actions' : '',
				$modal = $( '<div class="et_pb_modal_overlay' + on_top_class + on_top_both_actions_class + '" data-action="' + action + '"></div>' ),
				modal_interface = $( '#et-builder-prompt-modal-' + action ).length ? $( '#et-builder-prompt-modal-' + action ).html() : $( '#et-builder-prompt-modal' ).html(),
				modal_content = _.template( $( '#et-builder-prompt-modal-' + action + '-text' ).html() ),
				modal_attributes = {},
				$yes_no_button_wrapper,
				$yes_no_button,
				$yes_no_select;

			et_pb_close_all_right_click_options();

			// Lock body scroll
			$('body').addClass('et_pb_stop_scroll');

			if ( 'save_template' === action ) {
				var current_view = ET_PageBuilder_Layout.getView( cid_or_element.model.get( 'cid' ) ),
					parent_view = typeof current_view.model.get( 'parent' ) !== 'undefined' ? ET_PageBuilder_Layout.getView( current_view.model.get( 'parent' ) ) : '',
					$global_children = current_view.$el.find( '.et_pb_global' ),
					is_global = current_view.$el.is( '.et_pb_global' ),
					has_global = $global_children.length ? 'has_global' : 'no_globals';

				modal_attributes.is_global = typeof current_view.model.get( 'et_pb_global_module' ) !== 'undefined' && '' !== current_view.model.get( 'et_pb_global_module' ) ? 'global' : 'regular';
				modal_attributes.is_global_child = '' !== parent_view && ( ( typeof parent_view.model.get( 'et_pb_global_module' ) !== 'undefined' && '' !== parent_view.model.get( 'et_pb_global_module' ) ) || ( typeof parent_view.model.get( 'global_parent_cid' ) !== 'undefined' && '' !== parent_view.model.get( 'global_parent_cid' ) ) ) ? 'global' : 'regular';
				modal_attributes.module_type = current_view.model.get( 'type' );
			}

			if ( ! _.isUndefined( template_settings ) ) {
				$.extend( modal_attributes, template_settings );
			}

			$modal.append( modal_interface );

			$modal.find( '.et_pb_prompt_modal' ).prepend( modal_content( modal_attributes ) );

			if ( 'open_settings' === action ) {
				$modal.addClass( 'et_pb_builder_settings' );

				// update the shortcode
				et_pb_update_tracking_shortcode();

				$modal.find( '.et_pb_prompt_field_list' ).each(function() {
					var $field_list           = $(this),
						id                    = $field_list.attr( 'data-id' ),
						type                  = $field_list.attr( 'data-type' ),
						autoload              = $field_list.attr( 'data-autoload' ),
						custom_id             = {
							et_pb_enable_ab_testing: 'et_pb_use_ab_testing',
							et_pb_ab_refresh_interval: 'et_pb_ab_stats_refresh_interval'
						},
						$saving_input         = typeof custom_id[ id ] !== 'undefined' ? $( '#' + custom_id[ id ] ) : $( '#_' + id ),
						saved_value           = $saving_input.val();

					switch ( type ) {
						case ( 'yes_no_button' ) :
							var $yn_wrapper = $field_list.find( '.et_pb_yes_no_button_wrapper' ),
								$yn_button  = $yn_wrapper.find( '.et_pb_yes_no_button' ),
								$yn_select  = $yn_wrapper.find( 'select' );

							// Determine Y/N button state on load
							$yn_select.val( saved_value );
							$yn_select.trigger( 'change' );

							// On button click
							$yn_button.click( function() {
								var $yn_button = $(this);

								if ( $yn_button.hasClass( 'et_pb_off_state' ) ) {
									$yn_button.removeClass( 'et_pb_off_state' ).addClass( 'et_pb_on_state' );
									$yn_select.val( 'on' );
								} else {
									$yn_button.removeClass( 'et_pb_on_state' ).addClass( 'et_pb_off_state' );
									$yn_select.val( 'off' );
								}

								$yn_select.trigger( 'change' );
							});

							// On select change
							$yn_select.change( function() {
								var $yn_select = $(this),
									value      = $yn_select.val();

								if ( value === 'on' ) {
									$yn_button.removeClass( 'et_pb_off_state' ).addClass( 'et_pb_on_state' );
								} else {
									$yn_button.removeClass( 'et_pb_on_state' ).addClass( 'et_pb_off_state' );
								}

								// Updated affected ids
								if ( $field_list.data( 'affects' ) !== '' ) {
									var affects = $field_list.attr( 'data-affects' ),
										affected_ids = affects.split( '|' );

									_.each( affected_ids, function( affected_id ){
										var $selector = $modal.find( '.et_pb_prompt_field_list[data-id="' + affected_id + '"]' ),
											visibility_dependency = $selector.attr( 'data-visibility-dependency' );

										if ( value === visibility_dependency && $field_list.hasClass( 'et-pb-visible' ) ) {
											$selector.addClass( 'et-pb-visible' );
											$selector.find( 'select' ).trigger( 'change' );
										} else {
											$selector.removeClass( 'et-pb-visible' );

											// hide all dependant options if hidden option affects someting
											if ( $selector.data( 'affects' ) !== '' ) {
												var affected_ids =  $selector.data( 'affects' ).split( '|' );
												_.each( affected_ids, function( affected_id ) {
													var $selector = $modal.find( '.et_pb_prompt_field_list[data-id="' + affected_id + '"]' );

													$selector.removeClass( 'et-pb-visible' );
												} );
											}
										}
									});
								}
							});

							// Trigger select on load to initiate necesarry change
							$yn_select.trigger( 'change' );
							break;

						case ( 'color-alpha' ) :
							var $input_colorpicker = $field_list.find( '.input-colorpicker' );

								$input_colorpicker.val( saved_value ).wpColorPicker({
									width: 313
								});
							break;

						case ( 'colorpalette' ) :
							var $palette_wrapper               = $(this),
								$colorpalette_colorpickers     = $palette_wrapper.find( '.input-colorpalette-colorpicker' ),
								colorpalette_colorpicker_index = 0,
								saved_palette                  = saved_value.split( '|' );

							$colorpalette_colorpickers.each(function(){
								var $colorpalette_colorpicker      = $(this),
									colorpalette_colorpicker_color = saved_palette[ colorpalette_colorpicker_index ];

								$colorpalette_colorpicker.val( colorpalette_colorpicker_color ).wpColorPicker({
									hide : false,
									default : $(this).data( 'default-color' ),
									width: 313,
									palettes : false,
									change : function( event, ui ) {
										var $input     = $(this),
											data_index = $input.attr( 'data-index'),
											$preview   = $modal.find( '.colorpalette-item-' + data_index ),
											color      = ui.color.toString();

										$input.val( color );
										$preview.css({ 'backgroundColor' : color });
										set_this_editor_in_use();
									}
								});

								$colorpalette_colorpicker.trigger( 'change' );

								$colorpalette_colorpicker.siblings('.wp-picker-clear').on('click', function(e){
									e.preventDefault();

									$colorpalette_colorpicker.wpColorPicker( 'color', colorpalette_colorpicker_color )
								});

								$colorpalette_colorpicker.closest( '.wp-picker-container' ).find( '.wp-color-result' ).attr( 'title', et_pb_options.select_text );

								$colorpalette_colorpicker.closest( '.wp-picker-container' ).on( 'click', '.wp-color-result', function( event ) {
									// Hide active colorpalette colorpicker when "Select" button clicked
									$palette_wrapper.find( '.colorpalette-colorpicker' ).removeClass( 'active' );
								});

								colorpalette_colorpicker_index++;
							});

							$palette_wrapper.on( 'click', '.colorpalette-item', function(e){
								e.preventDefault();

								var $colorpalette_item = $(this),
									data_index         = $colorpalette_item.attr( 'data-index' );

								// Hide other colorpalette colorpicker
								$palette_wrapper.find( '.colorpalette-colorpicker').removeClass( 'active' );

								// Display selected colorpalette colorpicker
								$palette_wrapper.find( '.colorpalette-colorpicker[data-index="' + data_index + '"]').addClass( 'active' );
							});
							break;

						case ( 'range' ) :
							var $input_range           = $field_list.find( 'input[type="range"]' ),
								value                  = $input_range.val(),
								input_min              = $input_range.attr('min'),
								input_max              = $input_range.attr('max'),
								$input_wrapper         = $input_range.parent( 'div' ),
								$input_number_template = $( '<input />', { type : 'number', step : 1, class : 'et-pb-range-input', min : input_min, max : input_max, value : value }),
								$input_number;

							// Assign saved value
							$input_range.val( saved_value );

							// Append number input
							$input_wrapper.append( $input_number_template );

							$input_number = $input_wrapper.find( '.et-pb-range-input' );

							$input_range.on( 'change input', function() {
								var value = $(this).val();

								$input_number.val( value );
							});

							$input_number.on( 'change keydown', function(){
								var value = $(this).val();

								$input_range.val( value );
							});

							$input_range.trigger( 'change' );
							break;

						case( 'select' ) :
							var $select = $(this).find( 'select' );

							$select.val( saved_value );
							break;
						case( 'textarea' ) :
							var $textarea     = $(this).find( 'textarea' );

							$textarea.val( saved_value );
							break;
					}

				});
			}

			if ( 'view_ab_stats' === action ) {
				if ( $( '.et-pb-ab-view-stats-content' ).length ) {
					return;
				}

				// Start loading bar
				ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

				// Prevent cannot read undefined value
				if ( _.isUndefined( ET_PageBuilder_App.ab_stats ) ) {
					ET_PageBuilder_App.ab_stats = {};
				}

				if ( _.isUndefined( ET_PageBuilder_App.ab_stats[ et_pb_ab_js_options.refresh_interval_duration ] ) ) {
					// Get Split statistics data
					$.ajax({
						type : "POST",
						url : et_pb_options.ajaxurl,
						data : {
							action : 'et_pb_ab_builder_data',
							et_pb_ab_nonce : et_pb_options.ab_testing_builder_nonce,
							et_pb_ab_test_id : et_pb_ab_js_options.test_id,
							et_pb_ab_duration : et_pb_ab_js_options.refresh_interval_duration
						},
						success : function( data ) {
							// Stop loading bar
							ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

							data = $.parseJSON( data );

							// Save graph data for quick retrieval if there are data found
							ET_PageBuilder_App.ab_stats[ et_pb_ab_js_options.refresh_interval_duration ] = data;

							// Display all subjects graph
							ET_PageBuilder_AB_Testing.display_stats_tabs( data );
						}
					});
				} else {
					// Display all subjects graph
					setTimeout( function() {
						// Stop loading bar
						ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

						ET_PageBuilder_AB_Testing.display_stats_tabs( ET_PageBuilder_App.ab_stats[ et_pb_ab_js_options.refresh_interval_duration ] );
					}, 500 );
				}
			}

			if ( 'ab_testing_alert_yes_no' === action && ! _.isUndefined( template_settings.id ) ) {
				$modal.attr({ 'data-action' : action + '_' + template_settings.id });
			}

			$( 'body' ).append( $modal );

			if ( $('.et_pb_prompt_modal').css('bottom') === 'auto' ) {
				window.et_pb_align_vertical_modal( $modal.find('.et_pb_prompt_modal'), '.et_pb_prompt_buttons' );
			}

			setTimeout( function() {
				$modal.find('select, input, textarea, radio').filter(':eq(0)').focus();
			}, 1 );

			if ( 'rename_admin_label' === action ) {
				var admin_label = $modal.find( 'input#et_pb_new_admin_label' ),
					current_view = ET_PageBuilder_Layout.getView( cid_or_element ),
					current_admin_label = current_view.model.get( 'admin_label' ).trim();

				if ( current_admin_label !== '' ) {
					admin_label.val( current_admin_label );
				}
			}

			$( '.et_pb_modal_overlay .et_pb_prompt_proceed' ).click( function( event ) {
				event.preventDefault();

				var $prompt_modal = $(this).closest( '.et_pb_modal_overlay' );

				switch( $prompt_modal.data( 'action' ).trim() ){
					case 'deactivate_builder' :
						et_pb_deactivate_builder();
						break;
					case 'clear_layout' :
						ET_PageBuilder_App.removeAllSections( true );
						break;

					case 'rename_admin_label' :
						var admin_label = $prompt_modal.find( '#et_pb_new_admin_label' ).val().trim(),
							current_view = ET_PageBuilder_Layout.getView( cid_or_element );

						// TODO: Decide if we want to allow blank admin labels
						if ( admin_label == '' ) {
							$prompt_modal.find( '#et_pb_new_admin_label' ).focus()

							return;
						}

						current_view.model.set( 'admin_label', admin_label, { silent : true } );
						current_view.renameModule();

						// Enable history saving and set meta for history
						ET_PageBuilder_App.allowHistorySaving( 'renamed', 'module', admin_label );

						et_reinitialize_builder_layout();

						break;
					case 'reset_advanced_settings' :
						cid_or_element.each( function() {
							et_pb_reset_element_settings( $(this) );
						} );
						break;
					case 'save_layout' :
						var layout_name = $prompt_modal.find( '#et_pb_new_layout_name' ).val().trim();

						if ( layout_name == '' ) {
							$prompt_modal.find( '#et_pb_new_layout_name' ).focus()

							return;
						}

						$.ajax( {
							type: "POST",
							url: et_pb_options.ajaxurl,
							data:
							{
								action : 'et_pb_save_layout',
								et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
								et_layout_name : layout_name,
								et_layout_content : et_pb_get_content( 'content' ),
								et_layout_type : 'layout',
								et_post_type : et_pb_options.post_type
							},
							success: function( data ) {
								// update the list of saved layouts after new one was added
								et_load_saved_layouts( 'not_predefined', '', '', et_pb_options.post_type, true );
							}
						} );

						break;
					case 'save_template' :
						var template_name                = $prompt_modal.find( '#et_pb_new_template_name' ).val().trim(),
							layout_scope                 = $prompt_modal.find( $( '#et_pb_template_global' ) ).is( ':checked' ) ? 'global' : 'not_global',
							$module_settings_container   = $( '.et_pb_module_settings' ),
							module_type                  = $module_settings_container.data( 'module_type' ),
							layout_type                  = ( 'section' === module_type || 'row' === module_type ) ? module_type : 'module',
							module_width_upd             = typeof module_width !== 'undefined' ? module_width : 'regular',
							module_cid                   = cid_or_element.model.get( 'cid' ),
							template_shortcode           = '',
							selected_cats                = '',
							new_cat                      = $prompt_modal.find( '#et_pb_new_cat_name' ).val(),
							ignore_global                = is_global || ( typeof has_global !== 'undefined' && 'has_global' === has_global && 'global' === layout_scope ) ? 'ignore_global' : 'include_global',
							ignore_saved_tabs            = 'ignore_global' === ignore_global ? 'ignore_global_tabs' : '',
							$modal_settings_container    = $( '.et_pb_modal_settings_container' ),
							$modal_overlay               = $( '.et_pb_modal_overlay' );

							layout_type = 'row_inner' === module_type ? 'row' : layout_type;

						if ( template_name == '' ) {
							$prompt_modal.find( '#et_pb_new_template_name' ).focus();

							return;
						}

						if ( $( '.layout_cats_container input' ).is( ':checked' ) ) {

							$( '.layout_cats_container input' ).each( function() {
								var this_input = $( this );

								if ( this_input.is( ':checked' ) ) {
									selected_cats += '' !== selected_cats ? ',' + this_input.val() : this_input.val();
								}
							});

						}

						if ( 'module' === layout_type ) {
							cid_or_element.model.set( 'et_pb_saved_tabs', 'all', { silent : true } );
						}

						cid_or_element.performSaving();

						template_shortcode = ET_PageBuilder_App.generateCompleteShortcode( module_cid, layout_type, ignore_global, ignore_saved_tabs );

						if ( 'row_inner' === module_type ) {
							template_shortcode = template_shortcode.replace( /et_pb_row_inner/g, 'et_pb_row' );
							template_shortcode = template_shortcode.replace( /et_pb_column_inner/g, 'et_pb_column' );
						}

						$modal_settings_container.addClass( 'et_pb_modal_closing' );
						$modal_overlay.addClass( 'et_pb_overlay_closing' );

						setTimeout( function() {
							if ( 'module' === layout_type ) {
								et_pb_tinymce_remove_control( 'et_pb_content_new' );
							}

							$modal_settings_container.remove();
							$modal_overlay.remove();
							$( 'body' ).removeClass( 'et_pb_stop_scroll' );
						}, 600 );

						$.ajax( {
							type: "POST",
							url: et_pb_options.ajaxurl,
							dataType: 'json',
							data:
							{
								action : 'et_pb_save_layout',
								et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
								et_layout_name : template_name,
								et_layout_content : template_shortcode,
								et_layout_scope : layout_scope,
								et_layout_type : layout_type,
								et_module_width : module_width_upd,
								et_columns_layout : columns_layout,
								et_selected_tabs : 'all',
								et_module_type : module_type,
								et_layout_cats : selected_cats,
								et_layout_new_cat : new_cat,
								et_post_type : et_pb_options.post_type,
							},
							beforeSend: function( data ) {
								//show overlay which blocks the entire screen to avoid js errors if user starts editing the module immediately after saving
								if ( 'global' === layout_scope ) {
									if ( ! $( 'body' ).find( '.et_pb_global_loading_overlay' ).length ) {
										$( 'body' ).append( '<div class="et_pb_global_loading_overlay"></div>' );
									}
								}
							},
							success : function( data ) {
								if ( 'global' === layout_scope ) {
									var model = ET_PageBuilder_App.collection.find( function( model ) {
										return model.get( 'cid' ) == module_cid;
									} );

									model.set( 'et_pb_global_module', data.post_id );

									if ( 'ignore_global' === ignore_global ) {
										if ( $global_children.length ) {
											$global_children.each( function() {
												var child_cid = $( this ).data( 'cid' );

												if ( typeof child_cid !== 'undefined' && '' !== child_cid ) {
													var child_model = ET_PageBuilder_App.collection.find( function( model ) {
														return model.get( 'cid' ) == child_cid;
													} );

													child_model.unset( 'et_pb_global_module' );
													child_model.unset( 'et_pb_saved_tabs' );
												}
											});
										}
									}

									et_reinitialize_builder_layout();

									setTimeout( function(){
										$( 'body' ).find( '.et_pb_global_loading_overlay' ).remove();
									}, 650 );
								}

								// clear the library cache to update the library modules list
								$et_pb_templates_cache = {};
							}
						} );
						break;

					case 'open_settings' :
						var $enable_ab_testing_select = $modal.find( '#et_pb_enable_ab_testing' ),
							refresh_ab_stats_interval_value = $modal.find( '#et_pb_ab_refresh_interval' ).val(),
							enable_ab_testing_select_value = $enable_ab_testing_select.val() === 'on' ? true : false,
							shortcode_tracking_value = $modal.find( '#et_pb_enable_shortcode_tracking' ).val(),
							$refresh_ab_stats_interval_meta = $( '#et_pb_ab_stats_refresh_interval' ),
							$shortcode_tracking_value_meta = $( '#et_pb_enable_shortcode_tracking' );

							// Passes settings data to hidden inputs
							$modal.find( '.et_pb_prompt_field_list' ).each(function(){
								var $item = $( this ),
									id = $item.attr( 'data-id' ),
									autoload = $item.attr( 'data-autoload' ) === '1' ? true : false,
									$saving_input = $( '#_' + id ),
									saving_palette = [],
									initial_value = $saving_input.val();

								// Only pass autoload item
								if ( ! autoload ) {
									return;
								}

								if ( $item.hasClass( 'colorpalette' ) ) {
									$item.find( '.input-colorpalette-colorpicker' ).each(function() {
										saving_palette.push( $(this).val() );
									});

									$saving_input.val( saving_palette.join('|') );

									if ( initial_value !== saving_palette.join('|') ) {
										$saving_input.addClass( 'et_pb_value_updated' );

										set_this_editor_in_use();
									}

									// update option so updated color palette will be applied immediately
									et_pb_options.page_color_palette = saving_palette.join('|');
								} else {
									$saving_input.val( $item.find( '#' + id ).val() );

									if ( initial_value !== $item.find( '#' + id ).val() ) {
										$saving_input.addClass( 'et_pb_value_updated' );

										set_this_editor_in_use();
									}

									if ( 'et_pb_section_background_color' === $saving_input.attr( 'id' ) ) {
										et_pb_options.page_section_bg_color = $item.find( '#' + id ).val();
									}

									if ( 'et_pb_page_gutter_width' === $saving_input.attr( 'id' ) ) {
										et_pb_options.page_gutter_width = $item.find( '#' + id ).val();
									}
								}
							});

							// Update Split testing status meta data
							ET_PageBuilder_AB_Testing.toggle_status( enable_ab_testing_select_value );

							// Update Refresh stats interval meta data
							$refresh_ab_stats_interval_meta.val( refresh_ab_stats_interval_value );

							$shortcode_tracking_value_meta.val( shortcode_tracking_value );

							et_pb_ab_js_options.refresh_interval_duration = _.isUndefined( et_pb_ab_js_options.refresh_interval_durations[ refresh_ab_stats_interval_value ] ) ? 'day' : et_pb_ab_js_options.refresh_interval_durations[ refresh_ab_stats_interval_value ];

							if ( ET_PageBuilder_AB_Testing.is_active() ) {

								// Look for subject 1 of AB testing. If there isn't any, prompt subject selector mode
								if ( ET_PageBuilder_AB_Testing.count_subjects() < 2 ) {

									ET_PageBuilder_App.disable_publish = true;
									$( '#publish' ).addClass( 'disabled' );

									ET_PageBuilder_AB_Testing.check_create_db();

									// Prompt select subject modal box
									ET_PageBuilder_AB_Testing.alert_yes_no( 'select_ab_testing_subject' );

									// Turn on Split testing subject selection mode
									ET_PageBuilder_App.is_selecting_ab_testing_subject = true;

									// Adding nescesarry class for Split testing subject selection mode's UI
									$( '#et_pb_layout' ).addClass( 'et_pb_select_ab_testing_subject' );
								}

							} else {
								// Turn off Split testing subject selection mode
								ET_PageBuilder_App.is_selecting_ab_testing_subject = false;

								// Check against "on to off" or "off to off" state
								if ( ET_PageBuilder_AB_Testing.count_subjects() > 0 ) {
									et_pb_create_prompt_modal( 'turn_off_ab_testing' );
								}
							}
						break;

					case 'turn_off_ab_testing' :
						// Remove goal
						var ab_goals = ET_PageBuilder_Modules.where({ et_pb_ab_goal : 'on' }),
							ab_goal_view;

						_.each( ab_goals, function( ab_goal ){
							delete ab_goal.attributes.et_pb_ab_goal;

							if ( ! _.isUndefined( ab_goal.attributes.et_pb_temp_global_module ) || ! _.isUndefined( ab_goal.attributes.et_pb_temp_global_parent ) ) {
								ab_goal_view = ET_PageBuilder_Layout.getView( ab_goal.attributes.cid );

								ET_PageBuilder_Layout.removeTemporaryGlobalAttributes( ab_goal_view, true );
							}
						});

						// Disable publish button
						ET_PageBuilder_App.disable_publish = true;
						$( '#publish' ).addClass( 'disabled' );

						// Turn on Split Testing selecting winner mode
						ET_PageBuilder_App.is_selecting_ab_testing_winner = true;

						// Adding nescesarry class for Split testing winner selection mode's UI
						$( '#et_pb_layout' ).addClass( 'et_pb_select_ab_testing_winner' );

						break;

					case 'set_global_subject_winner' :
						// Remove Temporary global attributes
						ET_PageBuilder_Layout.removeTemporaryGlobalAttributes( template_settings.view );

						// Turn off Split testing sequence
						ET_PageBuilder_AB_Testing.turn_off_ab_testing_sequence();

						break;

					case 'ab_testing_alert' :
						if ( ! _.isUndefined( ET_PageBuilder_App.ab_last_visible_alert ) ) {
							delete ET_PageBuilder_App.ab_last_visible_alert;
						}
						break;

					case 'view_ab_stats' :
						// Set split test to off
						ET_PageBuilder_AB_Testing.toggle_status( false );

						// Turn off Split testing subject selection mode
						ET_PageBuilder_App.is_selecting_ab_testing_subject = false;

						// Check against "on to off" or "off to off" state
						if ( ET_PageBuilder_AB_Testing.count_subjects() > 0 ) {
							et_pb_create_prompt_modal( 'turn_off_ab_testing' );
						}
						break;
				}

				et_pb_close_modal( $( this ) );
			} );

			$( '.et_pb_modal_overlay .et_pb_prompt_proceed_alternative' ).click( function( event ) {
				event.preventDefault();

				var $prompt_modal = $(this).closest( '.et_pb_modal_overlay' );

				switch( $prompt_modal.data( 'action' ).trim() ){
					case 'set_global_subject_winner' :
						// Revive global attributes
						ET_PageBuilder_Layout.removeTemporaryGlobalAttributes( template_settings.view, true );

						// Turn off Split testing sequence
						ET_PageBuilder_AB_Testing.turn_off_ab_testing_sequence();

						break;

					case 'ab_testing_alert_yes_no_select_ab_testing_subject' :

						ET_PageBuilder_AB_Testing.toggle_status( false );

						ET_PageBuilder_AB_Testing.toggle_portability( true );

						// Re-enable publish
						ET_PageBuilder_App.disable_publish = false;
						$( '#publish' ).removeClass( 'disabled' );

						// Turn off Split testing subject selection mode
						ET_PageBuilder_App.is_selecting_ab_testing_subject = false;

						// Adding nescesarry class for Split testing subject selection mode's UI
						$( '#et_pb_layout' ).removeClass( 'et_pb_select_ab_testing_subject' );

						delete ET_PageBuilder_App.ab_last_visible_alert;

						break;
				}

				et_pb_close_modal( $( this ) );
			});

			$( '.et_pb_modal_overlay .et_pb_prompt_dont_proceed' ).click( function( event ) {
				event.preventDefault();

				var $prompt_modal = $(this).closest( '.et_pb_modal_overlay' );

				switch( $prompt_modal.data( 'action' ).trim()  ) {
					case 'ab_testing_alert' :
						if ( ! _.isUndefined( ET_PageBuilder_App.ab_last_visible_alert ) ) {
							delete ET_PageBuilder_App.ab_last_visible_alert;
						}
						break;
				}

				et_pb_close_modal( $( this ) );
			} );
		}

		function et_pb_update_tracking_shortcode() {
			var shortcode = '[et_pb_split_track id="' + et_pb_ab_js_options.test_id + '" /]';
			setTimeout( function(){
				$( '#et_pb_ab_current_shortcode' ).val( shortcode );
			}, 100 );
		}

		function et_pb_handle_clone_class( $element ) {
			$element.addClass( 'et_pb_animate_clone' );

			setTimeout( function() {
				if ( $element.length ) {
					$element.removeClass( 'et_pb_animate_clone' );
				}
			}, 500 );
		}

		function et_pb_close_modal( $this_button ) {
			var $modal_overlay = $this_button.closest( '.et_pb_modal_overlay' );

			// Unlock body scroll
			$('body').removeClass('et_pb_stop_scroll');

			$modal_overlay.addClass( 'et_pb_modal_closing' );

			setTimeout( function() {
				$modal_overlay.remove();
			}, 600 );
		}

		function et_pb_close_modal_view( that, trigger_event ) {
			that.removeOverlay();

			$( '.et_pb_modal_settings_container' ).addClass( 'et_pb_modal_closing' );

			setTimeout( function() {
				that.remove();

				if ( 'trigger_event' === trigger_event ) {
					ET_PageBuilder_Events.trigger( 'et-modal-view-removed' );
				}
			}, 600 );
		}

		function et_pb_hide_layout_settings(){
			if ( $et_pb_setting.filter( ':visible' ).length > 1 ){
				$et_pb_layout_settings.find('.et_pb_page_layout_settings').hide();
				$et_pb_layout_settings.find('.et_pb_side_nav_settings').show();
			}
			else{
				if ( 'post' !== et_pb_options.post_type ) {
					$et_pb_layout_settings.closest( '#et_settings_meta_box' ).find('.et_pb_page_layout_settings').hide();
				}

				$et_pb_layout_settings.closest( '#et_settings_meta_box' ).find('.et_pb_side_nav_settings').show();
				$et_pb_layout_settings.closest( '#et_settings_meta_box' ).find('.et_pb_single_title').show();
			}

			// On post, hide post format UI and layout settings if pagebuilder is activated
			if ( $post_format_wrapper.length ) {
				$post_format_wrapper.hide();

				var active_post_format = $post_format_wrapper.find( 'input[type="radio"]:checked').val();
				$( '.et_divi_format_setting.et_divi_' + active_post_format + '_settings' ).hide();
			}

			// Show project navigation option when builder enabled
			if ( 'project' === et_pb_options.post_type ) {
				$et_pb_layout_settings.closest( '#et_settings_meta_box' ).find( '.et_pb_project_nav' ).show();
			}
		}

		function et_pb_show_layout_settings(){
			$et_pb_layout_settings.show().closest( '#et_settings_meta_box' ).show();
			$et_pb_layout_settings.closest( '#et_settings_meta_box' ).find('.et_pb_side_nav_settings').hide();
			$et_pb_layout_settings.closest( '#et_settings_meta_box' ).find('.et_pb_single_title').hide();

			// On post, show post format UI and layout settings if pagebuilder is deactivated
			if ( $post_format_wrapper.length ) {
				$post_format_wrapper.show();

				var active_post_format = $post_format_wrapper.find( 'input[type="radio"]:checked').val();
				$( '.et_divi_format_setting.et_divi_' + active_post_format + '_settings' ).show();
			}

			// Hide project navigation option when builder disabled
			if ( 'project' === et_pb_options.post_type ) {
				$et_pb_layout_settings.closest( '#et_settings_meta_box' ).find( '.et_pb_project_nav' ).hide();
			}

		}

		function et_pb_get_content( textarea_id, fix_shortcodes ) {
			var content,
				fix_shortcodes = typeof fix_shortcodes !== 'undefined' ? fix_shortcodes : false;

			if ( typeof window.tinyMCE !== 'undefined' && window.tinyMCE.get( textarea_id ) && ! window.tinyMCE.get( textarea_id ).isHidden() ) {
				content = window.tinyMCE.get( textarea_id ).getContent();
			} else {
				content = $( '#' + textarea_id ).val();
			}

			if ( fix_shortcodes && typeof window.tinyMCE !== 'undefined' ) {
				content = content.replace( /<p>\[/g, '[' );
				content = content.replace( /\]<\/p>/g, ']' );
			}

			return content.trim();
		}

		function et_get_editor_mode() {
			var et_editor_mode = 'tinymce';

			if ( 'html' === getUserSetting( 'editor' ) ) {
				et_editor_mode = 'html';
			}

			return et_editor_mode;
		}

		function et_pb_is_editor_in_visual_mode( id ) {
			var is_editor_in_visual_mode = !! ( typeof window.tinyMCE !== 'undefined' && window.tinyMCE.get( id ) && ! window.tinyMCE.get( id ).isHidden() );

			return is_editor_in_visual_mode;
		}

		function set_this_editor_in_use() {
			var post_id = $('#post_ID').val();
			var secure = ( 'https:' === window.location.protocol );
			var cookie_expires = 5 * 60; // 5 mins (in secs)

			wpCookies.set( 'et-editor-available-post-' + post_id + '-bb', 'bb', cookie_expires * 6, et_pb_options.cookie_path, false, secure );
			wpCookies.set( 'et-editing-post-' + post_id + '-bb', 'bb', cookie_expires, et_pb_options.cookie_path, false, secure );
			wpCookies.remove( 'et-saving-post-' + post_id + '-bb', et_pb_options.cookie_path, false, secure );
			wpCookies.remove( 'et-saved-post-' + post_id + '-bb', et_pb_options.cookie_path, false, secure );
		}

		function et_pb_set_content( textarea_id, content, current_action ) {
			var current_action                = current_action || '',
				main_editor_in_visual_mode    = et_pb_is_editor_in_visual_mode( 'content' ),
				current_editor_in_visual_mode = et_pb_is_editor_in_visual_mode( textarea_id ),
				trimmed_content = $.trim( content );

			if ( typeof window.tinyMCE !== 'undefined' && window.tinyMCE.get( textarea_id ) && current_editor_in_visual_mode ) {
				var editor = window.tinyMCE.get( textarea_id );

				editor.setContent( trimmed_content, { format : 'html' } );
			}

			// Always set this content as well, since WP autosave uses, for example,
			// uses `$( '#content' ).val()` to fetch current post content
			if ( $( '#' + textarea_id ).length ) {
				$( '#' + textarea_id ).val( trimmed_content );
			}

			// initiate quicktags only once to avoid issue with duplication of tags
			if ( ! et_pb_quick_tags_init_done[textarea_id] && 'content' !== textarea_id ) {
				// generate quick tag buttons for the editor in Text mode
				( typeof tinyMCEPreInit.mceInit[textarea_id] !== "undefined" ) ? quicktags( { id : textarea_id } ) : quicktags( tinyMCEPreInit.qtInit[textarea_id] );
				QTags._buttonsInit();
				et_pb_quick_tags_init_done[textarea_id] = true;
			}

			// Enabling publish button + removes disable_publish mark
			if ( ! wp.heartbeat || ! wp.heartbeat.hasConnectionError() ) {
				$('#publish').removeClass( 'disabled' );

				delete ET_PageBuilder_App.disable_publish;
			}
		}

		function et_pb_tinymce_remove_control( textarea_id ) {
			if ( typeof window.tinyMCE !== 'undefined' ) {
				window.tinyMCE.execCommand( 'mceRemoveEditor', false, textarea_id );

				if ( typeof window.tinyMCE.get( textarea_id ) !== 'undefined' ) {
					window.tinyMCE.remove( '#' + textarea_id );
				}

				// set the quick tags init variable to false for current textarea so quicktags be initiated properly next time
				et_pb_quick_tags_init_done[textarea_id] = false;
			}
		}

		function et_pb_update_affected_fields( $affected_fields ) {
			if ( $affected_fields.length ) {
				$affected_fields.each( function() {
					$(this).trigger( 'change' );
				} );
			}
		}

		function et_pb_hide_empty_toggles( $option_el ) {
			if ( $option_el.lenght === 0 ) {
				return;
			}

			var $toggle_container = $option_el.closest( '.et-pb-options-toggle-container' );

			if ( $toggle_container.length === 0 ) {
				return;
			}

			var has_visible_options = false;

			// check if toggle has visible options by checking the `display` property of each option inside the toggle
			if ( $toggle_container.find( '.et-pb-option' ).length > 0 ) {
				$toggle_container.find( '.et-pb-option' ).each(function() {
					if ( 'none' !== $( this ).css( 'display' ) ) {
						has_visible_options = true;
					}
				});
			}

			// add/remove empty toggle class depending of visibility of options inside
			if ( has_visible_options ) {
				$toggle_container.removeClass( 'et-pb-options-toggle-empty' );
			} else {
				$toggle_container.addClass( 'et-pb-options-toggle-empty' );
			}
		}

		function et_pb_custom_color_remove( $element ) {
			var $this_el = $element,
				$color_picker_container = $this_el.closest( '.et-pb-custom-color-container' ),
				$color_choose_button = $color_picker_container.siblings( '.et-pb-choose-custom-color-button' ),
				$hidden_color_input = $color_picker_container.find( '.et-pb-custom-color-picker' ),
				hidden_class = 'et_pb_hidden';

			$color_choose_button.removeClass( hidden_class );
			$color_picker_container.addClass( hidden_class );

			$hidden_color_input.val( '' );

			return false;
		}

		// set default values for the responsive options.
		// Tablet default inherits Desktop value, Phone default inherits the Tablet value.
		function et_pb_update_mobile_defaults( $this_el, range_input_value ) {
			var this_device = typeof $this_el.data( 'device' ) === 'undefined' ? 'all' : $this_el.data( 'device' );

			if ( 'all' === this_device || 'phone' === this_device ) {
				return;
			}

			var this_value = typeof range_input_value !== 'undefined' ? range_input_value : $this_el.val(),
				is_range_field = $this_el.hasClass( 'et-pb-range-input' ) || $this_el.hasClass( 'et-pb-range' ),
				is_margin_field = $this_el.hasClass( 'et_custom_margin_main' ),
				field_class = is_range_field ? '.et-pb-range-input' : '.et-pb-main-setting',
				$tablet_field = $this_el.siblings( field_class + '.et_pb_setting_mobile_tablet' ),
				$phone_field = $this_el.siblings( field_class + '.et_pb_setting_mobile_phone' ),
				tablet_default = typeof $tablet_field.data( 'default' ) === 'undefined' ? '' : $tablet_field.data( 'default' ),
				phone_default = typeof $phone_field.data( 'default' ) === 'undefined' ? '' : $phone_field.data( 'default' ),
				range_value = _.isNaN( parseFloat( this_value ) ) ? 0 : parseFloat( this_value ),
				check_phone_default = false,
				$tablet_range,
				$phone_range;

			if ( is_range_field ) {
				$tablet_range = $this_el.siblings( '.et-pb-range.et_pb_setting_mobile_tablet' );
				$phone_range = $this_el.siblings( '.et-pb-range.et_pb_setting_mobile_phone' );
			} else if ( ! $this_el.hasClass( 'et_custom_margin_main' ) ) {
				this_value = et_pb_sanitize_input_unit_value( this_value, false, '' );
			}

			if ( 'desktop' === this_device ) {
				if ( 'no' === $tablet_field.data( 'has_saved_value' ) && $tablet_field.val() === tablet_default ) {
					$tablet_field.val( this_value ).change();
					check_phone_default = true;

					// update range value if needed
					if ( is_range_field ) {
						$tablet_range.val( range_value );
					}
				}

				$tablet_field.data( 'default', this_value );

				// update range value if needed
				if ( is_range_field ) {
					$tablet_range.data( 'default', range_value );
				}

				if ( is_margin_field ) {
					et_pb_process_custom_margin_field( $tablet_field );
				}
			} else {
				check_phone_default = true;
			}

			// adjust default settings for the phone
			if ( check_phone_default ) {
				if ( 'no' === $phone_field.data( 'has_saved_value' ) && $phone_field.val() === phone_default ) {
					$phone_field.val( this_value ).change();

					// update range value if needed
					if ( is_range_field ) {
						$phone_range.val( range_value );
					}

					if ( is_margin_field ) {
						et_pb_process_custom_margin_field( $phone_field );
					}
				}

				$phone_field.data( 'default', this_value );

				// update range value if needed
				if ( is_range_field ) {
					$phone_range.data( 'default', range_value );
				}
			}
		}

		function et_pb_update_reset_button( $option_container ) {
			var current_option = $option_container.find( '.et-pb-main-setting.et_pb_setting_mobile_active' ),
				option_value = current_option.val() + '',
				is_range_option  = current_option.hasClass( 'et-pb-range' ),
				option_default = typeof current_option.data( 'default' ) === 'undefined' ? '' : current_option.data( 'default' ) + '',
				$reset_button = $option_container.find( '.et-pb-reset-setting' ),
				option_default_processed = is_range_option && '' !== option_default ? parseFloat( option_default ) + '' : option_default;

			if ( option_value !== option_default_processed ) {
				$reset_button.addClass( 'et-pb-reset-icon-visible' );
			} else {
				$reset_button.removeClass( 'et-pb-reset-icon-visible' );
			}
		}

		function et_pb_open_responsive_tab( $option_container, selected_tab ) {
			$option_container.find( '.et_pb_setting_mobile' ).removeClass( 'et_pb_setting_mobile_active' );
			$option_container.find( '.et_pb_setting_mobile_' + selected_tab ).addClass( 'et_pb_setting_mobile_active' );
			$option_container.find( '.et_pb_mobile_settings_tab' ).removeClass( 'et_pb_mobile_settings_active_tab' );
			$option_container.find( '.et_pb_mobile_settings_tab[data-settings_tab="' + selected_tab + '"]' ).addClass( 'et_pb_mobile_settings_active_tab' );

			et_pb_update_reset_button( $option_container );
		}

		// check the advanced settings and update defaults based on the current settings of the parent module
		function et_pb_set_child_defaults( $container, module_cid ) {
			var $advanced_tab          = $container.find( '.et-pb-options-tab-advanced' ),
				$advanced_tab_settings = $advanced_tab.find( '.et-pb-main-setting' ),
				$parent_container      = $( '.et_pb_modal_settings_container:not(.et_pb_modal_settings_container_step2)'),
				$parent_container_adv  = $parent_container.find( '.et-pb-options-tab-advanced' ),
				current_module         = ET_PageBuilder_Modules.findWhere( { cid : module_cid } );

			if ( $advanced_tab.length ) {
				$advanced_tab_settings.each( function() {
					var $this_option = $( this ),
						$option_main_input,
						option_id;

					// process only range options
					if ( $this_option.hasClass( 'et-pb-range' ) ) {
						$option_main_input = $this_option.siblings( '.et-pb-range-input' );

						$option_main_input.each( function() {
							var $current_option = $( this ),
								option_id = $current_option.attr( 'id' ),
								current_device = typeof $current_option.data( 'device' ) !== 'undefined' ? $current_option.data( 'device' ) : 'all',
								option_parent = $( '#' + option_id );

							if ( option_parent.length ) {
								// check whether module already has module_defaults, otherwise set it to empty array
								current_module.attributes['module_defaults'] = current_module.attributes['module_defaults'] || [];
								// update 'module_defaults' to avoid saving the default values into database
								current_module.attributes['module_defaults'][ option_id ] = option_parent.val();
								// update default attribute in the option settings to display the correct value in builder
								if ( 'all' !== current_device ) {
									var $mobile_option = $current_option.siblings( '.et-pb-main-setting.et_pb_setting_mobile_' + current_device );

									$mobile_option.data( 'default_inherited', option_parent.val() );
									$mobile_option.data( 'default', option_parent.val() );
								}
								$current_option.data( 'default_inherited', option_parent.val() );
								$current_option.data( 'default', option_parent.val() );
							}
						} );
					}
				} );
			}
		}

		function et_pb_init_main_settings( $container, this_module_cid ) {
			var module                      = ET_PageBuilder_Modules.findWhere( { cid : this_module_cid } );
			var $main_tabs                  = $container.find( '.et-pb-options-tabs-links' );
			var $settings_tab               = $container.find( '.et-pb-options-tab' );

			var $et_affect_fields           = $container.find( '.et-pb-affects' );

			var $main_custom_margin_field   = $container.find( '.et_custom_margin_main' );
			var $custom_margin_fields       = $container.find( '.et_custom_margin' );

			var $font_select                = $container.find( 'select.et-pb-font-select' );
			var $font_style_fields          = $container.find( '.et_builder_font_style' );

			var $range_field                = $container.find( '.et-pb-range' );
			var $range_input                = $container.find( '.et-pb-range-input' );

			var $advanced_tab               = $container.find( '.et-pb-options-tab-advanced' );
			var $advanced_tab_settings      = $advanced_tab.find( '.et-pb-main-setting' );

			var $custom_color_picker        = $container.find( '.et-pb-custom-color-picker' );
			var $custom_color_choose_button = $container.find( '.et-pb-choose-custom-color-button' );

			var $select_with_option_groups  = $container.find( '.et-pb-option-container--select_with_option_groups select' );

			var $yes_no_button_wrapper      = $container.find( '.et_pb_yes_no_button_wrapper' );
			var $yes_no_button              = $container.find( '.et_pb_yes_no_button' );
			var $yes_no_select              = $container.find( '.et_pb_yes_no_button_wrapper select' );
			var $validate_unit_field        = $container.find( '.et-pb-validate-unit' );
			var $transparent_bg_option      = $container.find( '#et_pb_transparent_background' );
			var $options_wrapper            = $container.find( '.et_options_list:not(.et_conditional_logic)' );
			var $conditional_logic          = $container.find( '.et_conditional_logic' );
			var $background_fields          = $container.find( '.et-pb-option--background' );
			var $regular_input              = $container.find( 'input.regular-text.et_pb_setting_mobile' );
			var hidden_class                = 'et_pb_hidden';

			var $custom_css_option          = $container.find( '.et-pb-options-tab-custom_css .et-pb-option' );

			var $mobile_settings_toggle     = $container.find( '.et-pb-mobile-settings-toggle' );
			var $mobile_settings_tabs       = $container.find( '.et_pb_mobile_settings_tabs' );

			var $checkboxes_set             = $container.find( '.et_pb_checkboxes_wrapper' );
			var $checkbox                   = $checkboxes_set.find( 'input[type="checkbox"]' );

			var $section_bg_color_option    = 'section' === $container.data( 'module_type' ) ? $container.find( '#et_pb_background_color' ) : '';

			var $gutter_width_option        = $container.find( '#et_pb_gutter_width' );

			var $google_maps_api_option     = $container.find( '#et_pb_google_api_key' );
			var $google_maps_api_button     = $container.find( '.et_pb_update_google_key' );

			var $id_field                   = $container.find('#et_pb_field_id');

			if ( $google_maps_api_option.length ) {
				$google_maps_api_button.attr( 'href', et_pb_options.options_page_url );

				if ( '' === et_pb_options.google_api_key ) {
					$google_maps_api_option.addClass( 'et_pb_hidden_field' );
					$google_maps_api_button.text( $google_maps_api_button.data( 'empty_text' ) ).addClass( 'et_pb_no_field_visible' );
				} else {
					$google_maps_api_option.val( et_pb_options.google_api_key );
				}
			}

			if ( '' !== $section_bg_color_option && '' !== et_pb_options.page_section_bg_color ) {
				if ( '' === $section_bg_color_option.val() ) {
					$section_bg_color_option.val( et_pb_options.page_section_bg_color );
					$section_bg_color_option.change();
				}

				$section_bg_color_option.data( 'default', et_pb_options.page_section_bg_color );
			}

			if ( $gutter_width_option && '' !== et_pb_options.page_gutter_width ) {
				// update default gutters
				$gutter_width_option.siblings( '.et-pb-main-setting' ).data( 'default', et_pb_options.page_gutter_width );
				$gutter_width_option.data( 'default', et_pb_options.page_gutter_width );
			}

			if ( $mobile_settings_tabs.length ) {
				$mobile_settings_tabs.each( function() {
					var $this_tabs = $( this ),
						$this_option_container = $this_tabs.closest( '.et-pb-option' ),
						last_edited_field = $this_option_container.find( '.et_pb_mobile_last_edited_field' ).val(),
						$mobile_fields = $this_option_container.find( '.et_pb_setting_mobile' );

					// update defaults for the mobile settings
					if ( $mobile_fields.length ) {
						$mobile_fields.each( function() {
							var $this_field = $( this ),
								this_device = $this_field.data( 'device' ),
								has_saved_value = 'desktop' !== this_device && typeof $this_field.data( 'has_saved_value' ) !== 'undefined' ? $this_field.data( 'has_saved_value' ) : 'no',
								input_type = $this_field.attr( 'type' ),
								new_default = 'tablet' === this_device ? $this_field.siblings( 'input[type="' + input_type + '"].et_pb_setting_mobile_desktop' ).val() : $this_field.siblings( 'input[type="' + input_type + '"].et_pb_setting_mobile_tablet' ).val();

							// no need to update anything for desktop
							if ( 'desktop' === this_device ) {
								return;
							}

							if ( 'no' === has_saved_value ) {
								$this_field.val( new_default );
							}

							$this_field.data( 'default', new_default );
						});
					}

					if ( typeof last_edited_field !== 'undefined' && '' !== last_edited_field ) {
						last_edited_options = last_edited_field.split( '|' );

						if ( typeof last_edited_options[0] === 'undefined' || 'on' !== last_edited_options[0] ) {
							return;
						}

						$this_option_container.find( '.et-pb-mobile-settings-toggle' ).addClass( 'et-pb-mobile-icon-visible et-pb-mobile-settings-active' );
						$this_option_container.toggleClass( 'et_pb_has_mobile_settings' );


						if ( typeof last_edited_options[1] !== 'undefined' && '' !== last_edited_options[1] ) {
							et_pb_open_responsive_tab( $this_option_container, last_edited_options[1] );
						}
					}
				});
			}

			$mobile_settings_toggle.click( function() {
				var $this_toggle = $( this ),
					$this_option_container = $this_toggle.closest( '.et-pb-option' ),
					$last_edited_field = $this_option_container.find( '.et_pb_mobile_last_edited_field' ),
					last_edited_field_val = $last_edited_field.val(),
					last_edited_options = '' !== last_edited_field_val ? last_edited_field_val.split( '|' ) : [],
					active_tab = typeof last_edited_options[1] !== 'undefined' && '' !== last_edited_options[1] ? last_edited_options[1] : 'desktop',
					$reset_button = $this_option_container.find( '.et-pb-reset-setting' );

				$this_toggle.toggleClass( 'et-pb-mobile-settings-active' );
				$this_option_container.toggleClass( 'et_pb_has_mobile_settings' );

				// Set the last edited tab or desktop tab
				et_pb_open_responsive_tab( $this_option_container, active_tab );

				// Add et_pb_animate_options class to apply css animation and remove it after 500ms
				$this_option_container.addClass( 'et_pb_animate_options' );
				setTimeout( function() {
					$this_option_container.removeClass( 'et_pb_animate_options' );
				}, 500 );

				if ( $this_option_container.hasClass( 'et_pb_has_mobile_settings' ) ) {
					$reset_button.data( 'device', active_tab );
					last_edited_options[0] = 'on';
				} else {
					$reset_button.data( 'device', 'all' );
					last_edited_options[0] = 'off';
					et_pb_open_responsive_tab( $this_option_container, 'desktop' );
				}

				last_edited_options[1] = typeof last_edited_options[1] !== 'undefined' ? last_edited_options[1] : '';

				$last_edited_field.val( last_edited_options[0] + '|' + last_edited_options[1] );

				return false;
			});

			$mobile_settings_tabs.find( 'a' ).click( function() {
				var $this_button = $( this ),
					$option_container = $this_button.closest( '.et-pb-option-container' ),
					selected_tab = $this_button.data( 'settings_tab' ),
					$last_edited_field = $option_container.find( '.et_pb_mobile_last_edited_field' );

				$this_button.closest( '.et_pb_mobile_settings_tabs' ).find( 'a' ).removeClass( 'et_pb_mobile_settings_active_tab' );
				$this_button.addClass( 'et_pb_mobile_settings_active_tab' );

				$option_container.find( '.et_pb_setting_mobile' ).removeClass( 'et_pb_setting_mobile_active' );
				$option_container.find( '.et_pb_setting_mobile_' + selected_tab ).addClass( 'et_pb_setting_mobile_active' );

				$option_container.find( '.et-pb-reset-setting' ).data( 'device', selected_tab );

				$last_edited_field.val( 'on|' + selected_tab );

				et_pb_update_reset_button( $option_container );

				return false;
			});

			if ( $checkboxes_set.length ) {
				$checkboxes_set.each( function() {
					var $this_container = $( this ),
						value = $this_container.find( 'input.et-pb-main-setting' ).val(),
						checkboxes = $this_container.find( 'input[type="checkbox"]' ),
						values_array,
						i;

					if ( '' !== value ) {
						values_array = value.split( '|' );
						i = 0;

						checkboxes.each( function() {
							if ( 'on' === values_array[ i ] ) {
								var $this_checkbox = $( this );
								$this_checkbox.prop( 'checked', true );
							}
							i++;
						});
					}

				});
			}

			$checkbox.click( function() {
				var $this_checkbox = $( this ),
					current_checkbox_class = $( this ).attr( 'class' ),
					$this_container = $this_checkbox.closest( '.et_pb_checkboxes_wrapper' ),
					$disabled_option_field = $this_container.find( '.et_pb_disabled_option' ),
					$all_checkboxes = $this_container.find( 'input[type="checkbox"]' ),
					$value_field = $this_container.find( 'input.et-pb-main-setting' ),
					new_value = true === $this_checkbox.prop( 'checked' ) ? 'on' : 'off',
					i = 0,
					empty_values_array = [],
					checkbox_order,
					values_array;

					$all_checkboxes.each( function() {
						if ( $( this ).hasClass( current_checkbox_class ) ) {
							checkbox_order = i;
						}
						i++;
						empty_values_array.push( '' );
					});

					if ( '' !== $value_field.val() ) {
						values_array = $value_field.val().split( '|' );
					} else {
						values_array = empty_values_array;
					}

					values_array[ checkbox_order ] = new_value;

					$value_field.val( values_array.join( '|' ) );

					// need to check additional option for 'disable_on'
					if ( $disabled_option_field.length ) {
						if ( 'on' === values_array[0] && 'on' === values_array[1] && 'on' === values_array[2] ) {
							$disabled_option_field.val( 'on' );
						} else {
							$disabled_option_field.val( 'off' );
						}
					}
			});

			if ( typeof window.switchEditors !== 'undefined' ) {
				$container.find( '.wp-switch-editor' ).click( function() {
					var $this_el = $(this),
						editor_mode;

					editor_mode = $this_el.hasClass( 'switch-tmce' ) ? 'tinymce' : 'html';

					et_pb_maybe_apply_wpautop_to_models( editor_mode );

					window.switchEditors.go( 'content', editor_mode );
				} );
			}

			// fix the issue with disapperaing line breaks in visual editor

			$regular_input.on( 'input change' , function() {
				et_pb_update_mobile_defaults( $( this ) );
			});

			$custom_color_picker.each( function() {
				var $this_color_picker      = $(this),
					this_color_picker_value = $this_color_picker.val(),
					$container              = $this_color_picker.closest( '.et-pb-custom-color-container' ),
					$options_container      = $this_color_picker.closest( '.et-pb-options-tab' ),
					$choose_color_button    = $container.siblings( '.et-pb-choose-custom-color-button' ),
					old_color_option        = typeof $this_color_picker.data( 'old-option-ref' ) !== 'undefined' && '' !== $this_color_picker.data( 'old-option-ref' ) ? $this_color_picker.data( 'old-option-ref' ) : '',
					$old_color_el           = '' !== old_color_option ? $options_container.find( '.et-pb-option-' + old_color_option ) : '',
					$old_color_input        = '' !== $old_color_el && $old_color_el.length ? $old_color_el.find( 'input' ) : '',
					$main_color_picker      = $container.find( '.et-pb-color-picker-hex' );

				// process the value from old option if exists
				if ( '' !== $old_color_input ) {
					// get the value from old option only if new option is not set
					if ( '' === this_color_picker_value && '' !== $old_color_input.val() ) {
						this_color_picker_value = $old_color_input.val();
					}
					// always reset the old option to remove it from shortcode
					$old_color_input.val( '' );
				}

				if ( '' === this_color_picker_value ) {
					return true;
				}

				$container.removeClass( hidden_class );
				$choose_color_button.addClass( hidden_class );

				$main_color_picker.wpColorPicker( 'color', this_color_picker_value );
			} );

			$custom_color_choose_button.click( function() {
				var $this_el = $(this),
					$color_picker_container = $this_el.siblings( '.et-pb-custom-color-container' ),
					$color_picker = $color_picker_container.find( '.et-pb-color-picker-hex' ),
					$hidden_color_input = $color_picker_container.find( '.et-pb-custom-color-picker' );

				$this_el.addClass( hidden_class );
				$color_picker_container.removeClass( hidden_class );
				$color_picker_container.find( '.wp-color-result' ).click();

				$hidden_color_input.val( $color_picker.wpColorPicker( 'color' ) );

				return false;
			} );

			// calculate the value for transparent bg option if plugin activated
			if ( $transparent_bg_option.length && et_pb_options.is_plugin_used ) {
				var is_default_value = typeof $transparent_bg_option.data( 'default' ) !== 'undefined' && 'default' === $transparent_bg_option.data( 'default' ) ? true : false,
					bg_color_option_value = $container.find( '#et_pb_background_color' ).val(),
					module_transparent_background = module.attributes.et_pb_transparent_background,
					module_transparent_background_fb = module.attributes.et_pb_transparent_background_fb,
					is_transparent_background_fb = typeof module_transparent_background === 'undefined' && typeof module_transparent_background_fb !== 'undefined' && module_transparent_background_fb !== 'off';

				// default value for the option should be yes if custom color is not defined
				if ( ( is_default_value && '' === bg_color_option_value ) || is_transparent_background_fb ) {
					$transparent_bg_option.val( 'on' );
					$transparent_bg_option.trigger( 'change' );
				}
			}

			$select_with_option_groups.each( function() {
				var $value_field = $(this).siblings( 'input.et-pb-main-setting' ),
					values       = $value_field.val().split( '|' ),
					group        = values.length > 1 ? values[0] : '',
					value        = values.length > 1 ? values[1] : values[0];

				if ( '' === group ) {
					group = $(this).find(':selected').parent().attr('label');
					$value_field
						.val( group + '|' + value )
						.trigger( 'change' );
				}

				$(this).val( value );

				$(this).change( function() {
					var group = $(this).find(':selected').parent().attr('label') || '';
					$value_field
						.val( group + '|' + $(this).val() )
						.trigger( 'change' );
				});
			});

			$yes_no_button_wrapper.each( function() {
				var $this_el = $( this ),
					$this_switcher = $this_el.find( '.et_pb_yes_no_button' ),
					selected_value = $this_el.find( 'select' ).val();

				if ( 'on' === selected_value ) {
					$this_switcher.removeClass( 'et_pb_off_state' );
					$this_switcher.addClass( 'et_pb_on_state' );
				} else {
					$this_switcher.removeClass( 'et_pb_on_state' );
					$this_switcher.addClass( 'et_pb_off_state' );
				}
			});

			$options_wrapper.each( function() {
				var $current_wrapper = $(this),
					$options_list    = $current_wrapper.find( 'textarea' ),
					options_value    = $options_list.val(),
					debounce;

				$current_wrapper.on( 'keydown', 'input[type=text]', function(event) {
					if ('Enter' === event.key) {
						event.stopPropagation();
					}
				});

				$current_wrapper.on( 'keyup', 'input[type=text]', function(event) {
					clearTimeout( debounce );

					if ( 'Enter' === event.key ) {
						add_options_list_row( $(this), false, true );
					}

					debounce = setTimeout( function() {
						update_options_list( $current_wrapper, $options_list );
					}, 500 );
				} );

				$current_wrapper.on( 'click', '.et-pb-add-sortable-option', function( event ) {
					event.preventDefault();

					add_options_list_row( $(this), true, true );
				} );

				$current_wrapper.on( 'click', '.et_options_list_copy', function( event ) {
					event.preventDefault();

					add_options_list_row( $(this) );
				} );

				$current_wrapper.on( 'click', '.et_options_list_remove', function( event ) {
					event.preventDefault();

					if ( $current_wrapper.find( '.et_options_list_row' ).length === 1 ) {
						$current_wrapper.find( '.et_options_list_row:first input[type=text]' ).val( '' );
						update_options_list( $current_wrapper, $options_list );
						return;
					}

					var $parentRow = $(this).parents( '.et_options_list_row' );

					$parentRow.remove();

					update_options_list( $current_wrapper, $options_list );
				} );

				$current_wrapper.on( 'click', '.et_options_list_check', function( event ) {
					event.preventDefault();

					var $check = $(this);

					$current_wrapper.find( '.et_options_list_check' ).not( $check ).removeClass( 'et_options_list_checked' );

					$check.toggleClass( 'et_options_list_checked' );

					update_options_list( $current_wrapper, $options_list );
				} );

				init_options_list( $current_wrapper, $options_list, options_value );

				$current_wrapper.children( '.et_options_rows' ).sortable({
					axis : 'y',
					containment: 'parent',
					update: function() {
						update_options_list( $current_wrapper, $options_list );
						setTimeout( function() {
							$('.et_options_rows').children().css({
								position: '',
								top: '',
								left: ''
							});
						}, 700);
					}
				});
			} );

			function add_options_list_row( $button, newRow, reset ) {
				var rowSelector = '.et_options_list_row';

				if ( newRow ) {
					rowSelector = '.et_options_list_row:last';
				}

				// Target the clicked row
				var $parentRow = $button.parents( rowSelector );

				if ( newRow ) {
					$parentRow = $button.parent().find( rowSelector );
				}

				// Create a clone of the clicked row
				var $newRow = $parentRow.clone();

				// Remove the checked status for the new row to avoid multiple checked options
				$newRow.find( '.et_options_list_checked' ).removeClass( 'et_options_list_checked' );

				// Reset the value for new rows
				if ( reset ) {
					$newRow.find( 'input[type=text]' ).val('');
				}

				// Append the new row
				$newRow.insertAfter( $parentRow );
				$newRow.find( 'input[type=text]' ).focus();
			}

			function init_options_list( $wrapper, $options_list, options_value ) {
				if ( '' === options_value || _.isUndefined( options_value ) ) {
					return;
				}

				var $row  = $wrapper.find( '.et_options_list_row:first' );
				var $rows = $('<div>').addClass('et_options_rows');

				options_value = JSON.parse( options_value );

				for ( var i = 0; i < options_value.length; i++ ) {
					var option    = options_value[i];
					var isChecked = 1 === option.checked;

					var $newRow = $row.clone();
					$newRow.find( 'input[type=text]' ).val( option.value );
					$newRow.find( '.et_options_list_check' ).toggleClass( 'et_options_list_checked', isChecked );
					$newRow.appendTo( $rows );
				}

				$rows.insertAfter( $row );
				$row.remove();
			}

			function update_options_list( $wrapper, $options_list ) {
				var options = [];

				$options_list.val( '' );

				$wrapper.find( 'input[type=text]' ).each( function() {
					var $input        = $(this);
					var isChecked     = $input.prev( '.et_options_list_checked' ).length > 0;
					var option_object = {
						value: $input.val(),
						checked: 0
					};

					if ( '' === option_object.value ) {
						return;
					}

					if ( isChecked ) {
						option_object.checked = 1;
					}

					options.push( option_object );
				} );

				$options_list.val( JSON.stringify( options ) );
			}

			$conditional_logic.each( function() {
				/* Parse the conditional logic settings */
				var $logicSet = $(this);
				var logicData = $logicSet.find('textarea').val();
				logicData     = '' !== logicData ? logicData : '[]';
				logicData     = JSON.parse( logicData );

				/* Create an entry for each setting's row */
				var $row = $(this).find('.et_options_list_row');

				if ( 1 < logicData.length ) {
					for ( var i = 1; i < logicData.length; i++ ) {
						var $cloned_row = $row.clone();

						$logicSet.find('.et_options_list_row:last').after( $cloned_row );
					}
				}

				/* Wait for the Backbone templates to render */
				setTimeout( function() {
					/* Setup datastore */
					var datastore = {};
					var $wrapper  = $logicSet.parents( '.et_pb_modal_settings_container_step2' );
					var parentID  = $wrapper.data('parent-cid');
					var $parent   = $wrapper.prev( '.et_pb_modal_settings_container' );
					var $siblings = $parent.find( 'ul.et-pb-sortable-options li:not([data-cid="' + parentID + '"])' );
					var $field    = $logicSet.find('.et_conditional_logic_field');

					$siblings.each( function() {
						var $sibling     = $(this);
						var siblingCID   = $sibling.data('cid');
						var siblingData  = ET_PageBuilder_Modules.findWhere( { cid : siblingCID } );
						var siblingAttrs = siblingData.attributes;
						var fieldID      = siblingAttrs.et_pb_field_id;

						if ( '' === fieldID ) {
							return;
						}

						fieldID = fieldID.toLowerCase();

						var fieldType = ! _.isUndefined( siblingAttrs.et_pb_field_type ) ? siblingAttrs.et_pb_field_type : 'input';
						var $option   = $('<option data-type="' + fieldType + '" value="' + fieldID + '">' + siblingAttrs.et_pb_field_title + '</option>');

						$field.append( $option );

						datastore[fieldID] = siblingAttrs;
					});

					var firstKey = '';

					for (var prop in datastore) {
						if ( datastore.hasOwnProperty(prop) ) {
							firstKey = prop;
							break;
						}
					}

					var firstID = datastore[firstKey].et_pb_field_id.toLowerCase();

					if ( 0 === logicData.length && 0 !== datastore.length ) {
						logicData.push({
							field    : firstID,
							condition: 'is',
							value    : ''
						});
					}

					for ( var i = 0; i < logicData.length; i++ ) {
						var row            = logicData[i];
						var $row           = $logicSet.find('.et_options_list_row').eq(i);
						var $field         = $row.find('.et_conditional_logic_field');
						var $condition     = $field.next('.et_conditional_logic_condition');
						var fieldData      = datastore[firstID];
						var fieldType      = 'input';
						var fieldValue     = '';
						var fieldID        = _.isUndefined( row.field ) || _.isUndefined( datastore[row.field] ) ? firstID : row.field;
						var fieldCondition = _.isUndefined( row.condition ) ? 'is' : row.condition;

						if ( ! _.isUndefined( datastore[row.field] ) ) {
							fieldData  = datastore[row.field];
							fieldValue = row.value;
							fieldType  = _.isUndefined( fieldData.et_pb_field_type ) ? 'input' : fieldData.et_pb_field_type;
						}

						$row.find('.et_conditional_logic_value').remove();

						var $value = et_pb_create_conditional_logic_value_field( fieldType, fieldValue, fieldData, $logicSet );

						$value.insertAfter( $condition );

						$field.val( fieldID );
						$condition.val( fieldCondition );
					}

					et_pb_update_conditional_logic( $logicSet );

					$logicSet.on('change', '.et_conditional_logic_field', function() {
						var $field     = $(this);
						var field      = $field.val();
						var $condition = $field.next('.et_conditional_logic_condition');
						var $row       = $field.parents('.et_options_list_row');

						var fieldData     = datastore[field];
						var fieldValue    = '';
						var fieldType     = _.isUndefined( fieldData.et_pb_field_type ) ? 'input' : fieldData.et_pb_field_type;
						var selectedValue = $row.attr('data-selected-value');

						if ( selectedValue && ! _.includes(['radio', 'select', 'checkbox'], fieldType) ) {
							fieldValue = selectedValue;
						}

						$row.find('.et_conditional_logic_value').remove();

						var $value = et_pb_create_conditional_logic_value_field( fieldType, fieldValue, fieldData, $logicSet );

						$value.insertAfter( $condition );

						et_pb_update_conditional_logic( $logicSet );
					});

					$logicSet.on('change', '.et_conditional_logic_condition, .et_conditional_logic_value', function() {
						et_pb_update_conditional_logic( $logicSet );
					});

					$logicSet.on('click', '.et-pb-add-sortable-option', function(event) {
						event.preventDefault();

						et_pb_add_conditional_logic_row( $(this), datastore );
					});

					$logicSet.on('click', '.et_options_list_remove', function(event) {
						event.preventDefault();

						et_pb_remove_conditional_logic_row( $(this) );
					});
				}, 0);
			} );

			// Replace any spaces in field ID with underscores
			$id_field.on('focusout', function() {
				var sanitizedID = $(this).val().replace(/\s+/g, '_');

				var $wrapper  = $id_field.parents( '.et_pb_modal_settings_container_step2' );
				var $parent   = $wrapper.prev( '.et_pb_modal_settings_container' );
				var parentID  = $wrapper.data('parent-cid');
				var $siblings = $parent.find( 'ul.et-pb-sortable-options li:not([data-cid="' + parentID + '"])' );
				var siblings  = [];

				$siblings.each( function() {
					var $sibling     = $(this);
					var siblingCID   = $sibling.data('cid');
					var siblingData  = ET_PageBuilder_Modules.findWhere( { cid : siblingCID } );
					var siblingAttrs = siblingData.attributes;

					siblings.push( siblingAttrs );
				});

				// Assign a starting placeholder ID if it is blank
				if ( '' === sanitizedID ) {
					var currentData  = ET_PageBuilder_Modules.findWhere( { cid : parentID } );
					var module_order = parseInt( currentData.attributes.module_order );
					sanitizedID      = 'Field_' + ( module_order + 1 );
				}

				// Check siblings for duplicate IDs
				while(true) {
					var hasDuplicates = false;

					for ( var i = 0; i < siblings.length; i++ ) {
						var sibling = siblings[i];

						// Unlike VB we don't need to check if the sibling is the same
						// as the current module in BB, since we already exlude it
						// when generating the siblings data array

						var siblingID = sibling.et_pb_field_id;

						if ( siblingID.toLowerCase() === sanitizedID.toLowerCase() ) {
							sanitizedID	 = sanitizedID + '_2';
							hasDuplicates = true;
							break;
						}
					}

					if ( false === hasDuplicates ) {
						break;
					}
				}

				$(this).val( sanitizedID );
			});

			function et_pb_create_conditional_logic_value_field( fieldType, fieldValue, fieldData, $wrapper ) {
				var $value = null;

				switch( fieldType ) {
					case 'radio':
					case 'select':
						var optionsString = 'radio' === fieldType ? fieldData.et_pb_radio_options : fieldData.et_pb_select_options;

						options = et_pb_jsonify( optionsString );

						$value = $('<select></select>');

						$.each( options, function( optionIndex, optionData ) {
							var $option = $('<option value="' + optionData.value + '">' + optionData.value + '</option>');
							$value.append( $option );

							if ( '' === fieldValue ) {
								return;
							}

							$value.val( fieldValue );
						});

						break;
					case 'checkbox':
						$value        = $('<select></select>');

						var checked   = $wrapper.data('checked');
						var unchecked = $wrapper.data('unchecked');

						$value.append( '<option value="checked">' + checked + '</option>' );
						$value.append( '<option value="not checked">' + unchecked + '</option>' );

						if ( ! _.includes(['checked', 'not checked'], fieldValue) ) {
							fieldValue = 'checked';
						}

						$value.val( fieldValue );

						break;
					default:
						$value = $('<input type="text" value="' + fieldValue + '" />');
						break;
				}

				$value.addClass('et_conditional_logic_value');

				return $value;
			}

			function et_pb_add_conditional_logic_row( $button, datastore ) {
				if ( 0 === datastore.length ) {
					return;
				}

				var $wrapper   = $button.parents('.et_conditional_logic');
				var $row       = $wrapper.find('.et_options_list_row:last');
				var $newRow    = $row.clone();
				var $condition = $newRow.find('.et_conditional_logic_condition');

				var firstKey = '';

				for (var prop in datastore) {
					if ( datastore.hasOwnProperty(prop) ) {
						firstKey = prop;
						break;
					}
				}

				var fieldData  = datastore[firstKey];
				var fieldValue = '';
				var fieldType  = _.isUndefined( fieldData.et_pb_field_type ) ? 'input' : fieldData.et_pb_field_type;

				$newRow.find('.et_conditional_logic_value').remove();

				var $value = et_pb_create_conditional_logic_value_field( fieldType, fieldValue, fieldData, $wrapper );

				$value.insertAfter( $condition );

				$condition.val( 'is' );

				$newRow.insertAfter($row);

				et_pb_update_conditional_logic( $wrapper );
			}

			function et_pb_remove_conditional_logic_row( $button ) {
				var $row     = $button.parent('.et_options_list_row');
				var $wrapper = $row.parent('.et_conditional_logic');

				if ( 1 === $wrapper.find('.et_options_list_row').length ) {
					return;
				}

				$row.remove();

				et_pb_update_conditional_logic( $wrapper );
			}

			function et_pb_update_conditional_logic( $wrapper ) {
				var $textarea = $wrapper.find('textarea');
				var logic     = [];

				$wrapper.find('.et_options_list_row').each(function() {
					var $row          = $(this);
					var field         = $row.find('.et_conditional_logic_field').val();
					var condition     = $row.find('.et_conditional_logic_condition').val();
					var $value        = $row.find('.et_conditional_logic_value');
					var value         = $value.val();
					var fieldType     = $row.find('.et_conditional_logic_field').find('option:selected').data('type');
					var selectedValue = _.includes(['radio', 'select', 'checkbox'], fieldType) ? false : value;
					var logicSet      = {
						field    : field,
						condition: condition,
						value    : value
					};

					if ( false !== selectedValue ) {
						$row.attr('data-selected-value', selectedValue);
					}

					logic.push( logicSet );

					$value.prop('disabled', false);

					if ( _.includes(['is empty', 'is not empty'], condition) ) {
						$value.prop('disabled', true);
					}
				});

				$textarea.val( JSON.stringify( logic ) );
			}

			function et_pb_jsonify( escapedString ) {
				if ( '' === escapedString ) {
					escapedString = '[]';
				}

				var unescapedString;

				unescapedString = escapedString.replace( /%22/g, "\"" );
				unescapedString = unescapedString.replace( /%91/g, "[" );
				unescapedString = unescapedString.replace( /%92/g, "\\" );
				unescapedString = unescapedString.replace( /%93/g, "]" );

				var string_json  = JSON.parse( unescapedString );

				return string_json;
			}

			$yes_no_button.click( function() {
				var $this_el = $( this ),
					$this_select = $this_el.closest( '.et_pb_yes_no_button_wrapper' ).find( 'select' );

				if ( $this_el.hasClass( 'et_pb_off_state') ) {
					$this_el.removeClass( 'et_pb_off_state' );
					$this_el.addClass( 'et_pb_on_state' );
					$this_select.val( 'on' );
				} else {
					$this_el.removeClass( 'et_pb_on_state' );
					$this_el.addClass( 'et_pb_off_state' );
					$this_select.val( 'off' );
				}

				$this_select.trigger( 'change' );

			});

			$yes_no_select.change( function() {
				var $this_el = $( this ),
					$this_switcher = $this_el.closest( '.et_pb_yes_no_button_wrapper' ).find( '.et_pb_yes_no_button' ),
					new_value = $this_el.val();

				if ( 'on' === new_value ) {
					$this_switcher.removeClass( 'et_pb_off_state' );
					$this_switcher.addClass( 'et_pb_on_state' );
				} else {
					$this_switcher.removeClass( 'et_pb_on_state' );
					$this_switcher.addClass( 'et_pb_off_state' );
				}

			});

			if ( $background_fields.length ) {
				$background_fields.each(function() {
					var $background_fields_ui = $(this);

					$background_fields_ui.find('.et_pb_background-tab:first').show();
					$background_fields_ui.find('.et_pb_background-tab-navs li:first a').addClass('active');

					// Background field tab navigation
					$background_fields_ui.find('.et_pb_background-tab-navs').on( 'click', 'a', function(e) {
						e.preventDefault();

						var $clicked_tab = $(this),
							clicked_tab = $clicked_tab.attr('data-tab'),
							$wrapper = $clicked_tab.closest('.et-pb-option--background');

						$wrapper.find('.et_pb_background-tab-navs a').removeClass('active');
						$clicked_tab.addClass('active');
						$wrapper.find('.et_pb_background-tab:visible').hide();
						$wrapper.find('.et_pb_background-tab[data-tab="' + clicked_tab + '"]').fadeIn();
						$wrapper.find('.et_pb_background-tab[data-tab="' + clicked_tab + '"]').find( '.et-pb-affects' ).trigger( 'change' );

						et_pb_update_background_tab_filled_status( $clicked_tab.closest( '.et-pb-option--background' ), false );
					});

					// Gradient preview
					var $gradient_preview_tab    = $background_fields_ui.find('.et_pb_background-tab--gradient'),
						$gradient_preview        = $gradient_preview_tab.find('.et-pb-option-preview'),
						$gradient_preview_add    = $gradient_preview.find('.et-pb-option-preview-button--add'),
						$gradient_preview_swap   = $gradient_preview.find('.et-pb-option-preview-button--swap'),
						$gradient_preview_delete = $gradient_preview.find('.et-pb-option-preview-button--delete'),
						$use_gradient_ui         = $gradient_preview_tab.find('.et_pb_background-option--use_background_color_gradient .et_pb_yes_no_button'),
						$use_gradient_input      = $gradient_preview_tab.find('.et_pb_background-option--use_background_color_gradient select'),
						$gradient_start_input    = $gradient_preview_tab.find( '.et_pb_background-option--background_color_gradient_start .wp-color-picker' ),
						gradient                 = et_pb_get_gradient( $gradient_preview_tab );

					// Has gradient color
					if ( gradient ) {
						$gradient_preview.css({
							backgroundImage: gradient
						});

						$gradient_preview.removeClass( 'et-pb-option-preview--empty' );
					}

					$gradient_preview.click(function(e) {
						e.preventDefault();
						e.stopPropagation();

						if ( ! $gradient_preview.hasClass('et-pb-option-preview--empty') ) {
							$gradient_start_input.wpColorPicker('open', true);
							return;
						}

						$use_gradient_ui.removeClass( 'et_pb_off_state' ).addClass( 'et_pb_on_state' );
						$use_gradient_input.val( 'on' ).trigger( 'change' );

						$gradient_preview.removeClass( 'et-pb-option-preview--empty' );
					});

					$gradient_preview_add.click(function(e) {
						e.stopPropagation();
						$use_gradient_ui.removeClass( 'et_pb_off_state' ).addClass( 'et_pb_on_state' );
						$use_gradient_input.val( 'on' ).trigger( 'change' );

						$gradient_preview.removeClass( 'et-pb-option-preview--empty' );
					});

					$gradient_preview_delete.click(function(e) {
						e.stopPropagation();

						$use_gradient_ui.removeClass( 'et_pb_on_state' ).addClass( 'et_pb_off_state' );
						$use_gradient_input.val( 'off' ).trigger( 'change' );

						$gradient_preview.addClass( 'et-pb-option-preview--empty' );
					});

					$gradient_preview_swap.click(function(e) {
						e.preventDefault();
						e.stopPropagation();

						var $background = $(this).closest( '.et-pb-option--background' ),
							$start      = $background.find( '.et_pb_background-option--background_color_gradient_start .et-pb-color-picker-hex' ),
							$end        = $background.find( '.et_pb_background-option--background_color_gradient_end .et-pb-color-picker-hex' ),
							startValue  = $start.val(),
							endValue    = $end.val();

						// Swap color
						$start.val( endValue ).trigger( 'change' );
						$end.val( startValue ).trigger( 'change' );
					});

					$gradient_preview_tab.on( 'change input keyup', 'select, input[type="text"], input[type="range"]', function() {
						et_pb_update_gradient_preview( $(this) );
					});

					// Determine filled tab state
					function et_pb_update_background_tab_filled_status( $background_fields_ui, activate_filled_tab ) {
						$background_fields_ui.find( '.et_pb_background-tab' ).each(function() {
							var $background_tab     = $(this),
								tab_status          = false,
								background_tab_name = $background_tab.attr( 'data-tab' ),
								$background_tab_nav = $background_fields_ui.find( '.et_pb_background-tab-nav[data-tab="' + background_tab_name + '"]' );

							switch( background_tab_name ) {
								case( 'color' ) :
									var $use_background_color = $background_tab.find( '.et_pb_background-option--use_background_color' ),
										$background_color = $background_tab.find( '.et_pb_background-option--background_color' ),
										$module_bg_color = $background_tab.find( '.et_pb_background-option--module_bg_color' );

									if ( $use_background_color.length && $background_color.length ) {
										tab_status = 'on' === $use_background_color.find( 'select option:selected' ).val() && '' !== $background_color.find( '.et-pb-color-picker-hex' ).val();
									} else if ( ! $use_background_color.length && $background_color.length ) {
										tab_status = '' !== $background_color.find( '.et-pb-color-picker-hex' ).val();
									} else if ( $module_bg_color.length ) {
										tab_status = '' !== $module_bg_color.find( '.et-pb-color-picker-hex' ).val();
									}

									break;
								case( 'gradient' ) :
									var use_background_color_gradient = $background_tab.find( '.et_pb_background-option--use_background_color_gradient select option:selected' ).val();

									if ( 'on' === use_background_color_gradient ) {
										tab_status = true;
									}

									break;
								case( 'image' ) :
									var background_image = $background_tab.find( '.et_pb_background-option--background_image .et-pb-upload-field' ).val() || $background_tab.find( '.et_pb_background-option--background_url .et-pb-upload-field' ).val();

									if ( ! _.isUndefined( background_image ) && '' !== background_image ) {
										tab_status = true;
									}

									break;
								case( 'video' ) :
									var background_video_mp4 = $background_tab.find( '.et_pb_background-option--background_video_mp4 .et-pb-upload-field' ).val() || $background_tab.find( '.et_pb_background-option--video_bg_mp4 .et-pb-upload-field' ).val(),
										background_video_webm = $background_tab.find( '.et_pb_background-option--background_video_webm .et-pb-upload-field' ).val() || $background_tab.find( '.et_pb_background-option--video_bg_webm .et-pb-upload-field' ).val();

									if ( ( ! _.isUndefined( background_video_mp4 ) && '' !== background_video_mp4 ) || ( ! _.isUndefined( background_video_webm ) && '' !== background_video_webm ) ) {
										tab_status = true;
									}

									break;
							}

							if ( tab_status ) {
								$background_tab_nav.addClass( 'et-pb-filled' );

								if ( activate_filled_tab ) {
									$background_fields_ui.find( '.et_pb_background-tab' ).removeAttr( 'style' );
									$background_tab_nav.trigger( 'click' );
								}
							} else {
								$background_tab_nav.removeClass( 'et-pb-filled' );
							}
						});
					}

					et_pb_update_background_tab_filled_status( $background_fields_ui, true );

					// Update image preview panel whenever these fields are changed
					var column_index = $background_fields_ui.find( '.et-pb-option-container--background' ).attr( 'data-column-index' ),
						field_suffix = typeof column_index === 'undefined' ? '' : '_' + column_index,
						image_preview_affecting_fields = [
							'#et_pb_background_color' + field_suffix,
							'#et_pb_module_bg_color', // Post Title
							'#et_pb_use_background_color', // Call to Action
							'#et_pb_background_color_gradient_start' + field_suffix,
							'#et_pb_background_color_gradient_end' + field_suffix,
							'#et_pb_use_background_color_gradient' + field_suffix,
							'#et_pb_background_color_gradient_type' + field_suffix,
							'#et_pb_background_color_gradient_direction' + field_suffix,
							'#et_pb_background_color_gradient_direction_radial' + field_suffix,
							'#et_pb_background_color_gradient_start_position' + field_suffix,
							'#et_pb_background_color_gradient_end_position' + field_suffix,
							typeof column_index === 'undefined' ? '#et_pb_bg_img' + field_suffix : '#et_pb_background_image',
							'#et_pb_background_url', // Fullwidth Header
							'#et_pb_parallax' + field_suffix,
							'#et_pb_parallax_effect',  // Post Title
							'#et_pb_parallax_method' + field_suffix,
							'#et_pb_background_size' + field_suffix,
							'#et_pb_background_position' + field_suffix,
							'#et_pb_background_repeat' + field_suffix,
							'#et_pb_background_blend' + field_suffix,
							'.et-pb-range'
						];

					$background_fields_ui.find( image_preview_affecting_fields.join( ', ' ) ).change( function() {
						et_pb_generate_preview_content( $background_fields_ui.find( '.et_pb_background-tab--image .et-pb-upload-button' ) );
					} );
				});
			}

			$main_tabs.find( 'li a' ).click( function() {
				var $this_el              = $(this),
					tab_index             = $this_el.closest( 'li' ).index(),
					$links_container      = $this_el.closest( 'ul' ),
					$tabs                 = $links_container.siblings( '.et-pb-options-tabs' ),
					active_link_class     = 'et-pb-options-tabs-links-active',
					$active_tab_link      = $links_container.find( '.' + active_link_class ),
					active_tab_link_index = $active_tab_link.index(),
					$current_tab          = $tabs.find( '.et-pb-options-tab' ).eq( active_tab_link_index ),
					$next_tab             = $tabs.find( '.et-pb-options-tab' ).eq( tab_index ),
					fade_speed            = 300;

				if ( active_tab_link_index !== tab_index ) {
					$next_tab.css( { 'display' : 'none', opacity : 0 } );

					$current_tab.css( { 'display' : 'block', 'opacity' : 1 } ).stop( true, true ).animate( { opacity : 0 }, fade_speed, function(){
						$(this).css( 'display', 'none' );

						$next_tab.css( { 'display' : 'block', 'opacity' : 0 } ).stop( true, true ).animate( { opacity : 1 }, fade_speed, function() {
							var $this = $(this);

							//et_pb_update_affected_fields( $et_affect_fields );

							if ( ! $this.find( '.et-pb-option:visible' ).length && ! $next_tab.hasClass( 'et-pb-options-tab-view_stats' ) ) {
								$this.append( '<p class="et-pb-all-options-hidden">' + et_pb_options.all_tab_options_hidden + '<p>' );
							} else {
								$('.et-pb-all-options-hidden').remove();
							}

							$main_tabs.trigger( 'et_pb_main_tab:changed' );
						} );
					} );

					$active_tab_link.removeClass( active_link_class );

					$links_container.find( 'li' ).eq( tab_index ).addClass( active_link_class );

					// always scroll to the top when tab opened
					$( '.et-pb-options-tabs' ).animate( { scrollTop :  0 }, 400, 'swing' );
				}

				return false;
			} );

			$settings_tab.each( function() {
				var $this_tab          = $(this),
					$toggles           = $this_tab.find( '.et-pb-options-toggle-enabled' ),
					open_class         = 'et-pb-option-toggle-content-open',
					closed_class       = 'et-pb-option-toggle-content-closed',
					content_area_class = 'et-pb-option-toggle-content',
					animation_speed    = 300;

				$toggles.find( 'h3' ).click( function() {
					var $this_el                  = $(this),
						$content_area             = $this_el.siblings( '.' + content_area_class ),
						$container                = $this_el.closest( '.et-pb-options-toggle-container' ),
						$open_toggle              = $toggles.filter( '.' + open_class ),
						$open_toggle_content_area = $open_toggle.find( '.' + content_area_class );

					if ( $container.hasClass( open_class ) ) {
						return;
					}

					$open_toggle.removeClass( open_class ).addClass( closed_class );
					$open_toggle_content_area.slideToggle( animation_speed );

					$container.removeClass( closed_class ).addClass( open_class );
					$content_area.slideToggle( animation_speed, function() {
						et_pb_update_affected_fields( $et_affect_fields );
					} );
				} );
			} );

			if ( $main_custom_margin_field.length ) {
				$main_custom_margin_field.each( function() {
					et_pb_process_custom_margin_field( $( this ) );
				});

				$main_custom_margin_field.on( 'et_main_custom_margin:change', function() {
					et_pb_process_custom_margin_field( $(this) );
				} );
			}

			$custom_margin_fields.change( function() {
				var $this_el    = $(this),
					this_device = typeof $this_el.data( 'device' ) !== 'undefined' ? $this_el.data( 'device' ) : 'all',
					$container  = $this_el.closest( '.et_custom_margin_padding' ),
					$main_container = $container.closest( '.et-pb-option-container' ),
					$mobile_toggle = $main_container.find( '.et-pb-mobile-settings-toggle' ),
					$main_field = 'all' === this_device ? $container.find( '.et_custom_margin_main' ) : $container.find( '.et_custom_margin_main.et_pb_setting_mobile_' + this_device ),
					fields_selector = 'all' === this_device ? '.et_custom_margin' : '.et_custom_margin.et_pb_setting_mobile_' + this_device,
					margin      = '';

				$container.find( fields_selector ).each( function() {
					margin += $.trim( et_pb_sanitize_input_unit_value( $(this).val(), $(this).hasClass( 'auto_important' ) ) ) + '|';
				} );

				margin = margin.slice( 0, -1 );

				if ( '|||' === margin ) {
					margin = '';
				} else {
					$mobile_toggle.addClass( 'et-pb-mobile-icon-visible' );
				}

				$main_field.val( margin ).trigger( 'et_pb_setting:change' );

				et_pb_update_mobile_defaults( $main_field );
			} );

			$font_style_fields.click( function() {
				var $this_el = $(this);

				$this_el.toggleClass( 'et_font_style_active' );

				$font_select.trigger( 'change' );

				return false;
			} );

			$font_select.change( function() {
				var $this_el           = $(this),
					$main_option       = $this_el.siblings( 'input.et-pb-font-select' ),
					$style_options     = $this_el.siblings( '.et_builder_font_styles' ),
					$bold_option       = $style_options.find( '.et_builder_bold_font' ),
					$italic_option     = $style_options.find( '.et_builder_italic_font' ),
					$uppercase_option  = $style_options.find( '.et_builder_uppercase_font' ),
					$underline_option  = $style_options.find( '.et_builder_underline_font' ),
					style_active_class = 'et_font_style_active',
					font_name          = $this_el.val(),
					result             = '';

				result += font_name !== 'default' ? $.trim( font_name ) : '';

				result += '|';

				if ( $bold_option.hasClass( style_active_class ) ) {
					result += 'on';
				}

				result += '|';

				if ( $italic_option.hasClass( style_active_class ) ) {
					result += 'on';
				}

				result += '|';

				if ( $uppercase_option.hasClass( style_active_class ) ) {
					result += 'on';
				}

				result += '|';

				if ( $underline_option.hasClass( style_active_class ) ) {
					result += 'on';
				}

				$main_option.val( result ).trigger( 'change' );
			} );

			$font_select.each( function() {
				et_pb_setup_font_setting( $(this), false );
			} );

			$range_field.on( 'input change', function() {
				var $this_el          = $(this),
					this_device       = typeof $this_el.data( 'device' ) === 'undefined' ? 'all' : $this_el.data( 'device' ),
					range_value       = $this_el.val(),
					$range_input      = 'all' === this_device ? $this_el.siblings( '.et-pb-range-input' ) : $this_el.siblings( '.et-pb-range-input.et_pb_setting_mobile_' + this_device ),
					initial_value_set = $range_input.data( 'initial_value_set' ) || false,
					range_input_value = et_pb_sanitize_input_unit_value( $.trim( $range_input.val() ), false, 'no_default_unit' ),
					number,
					length;

				if ( range_input_value === '' && ! initial_value_set ) {
					$this_el.val( 0 );
					$range_input.data( 'initial_value_set', true );

					return;
				}

				number = parseFloat( range_input_value );

				range_input_value += '';

				length = $.trim( range_input_value.replace( number, '' ) );

				if ( length !== '' ) {
					range_value += length;
				}

				$range_input.val( range_value );

				et_pb_update_mobile_defaults( $this_el, range_value );

			} );

			if ( $range_field.length ) {
				$range_field.each( function() {
					var $this_el          = $(this),
						this_device       = typeof $this_el.data( 'device' ) === 'undefined' ? 'all' : $this_el.data( 'device' ),
						default_value     = typeof $this_el.data( 'default_inherited' ) !== 'undefined' ? $.trim( $this_el.data( 'default_inherited' ) ) : $.trim( $this_el.data( 'default' ) ),
						$range_input      = 'all' === this_device ? $this_el.siblings( '.et-pb-range-input' ) : $this_el.siblings( '.et-pb-range-input.et_pb_setting_mobile_' + this_device ),
						range_input_value = $.trim( $range_input.val() );

					if ( range_input_value === '' ) {
						if ( default_value !== '' ) {
							$range_input.val( default_value );

							default_value = parseFloat( default_value ) || 0;
						}

						$this_el.val( default_value );
						range_input_value = default_value;
					}

					// Define defaults for tablet and phone settings on load
					if ( 'tablet' === this_device ) {
						var $desktop_field = $this_el.siblings( '.et-pb-range-input.et_pb_setting_mobile_desktop' ),
							new_tablet_default = $desktop_field.val();

						$this_el.data( 'default', parseFloat( new_tablet_default ) );
						$range_input.data( 'default', new_tablet_default );

					} else if ( 'phone' === this_device ) {
						var $tablet_field = $this_el.siblings( '.et-pb-range-input.et_pb_setting_mobile_tablet' ),
							new_phone_default = $tablet_field.val();

						$this_el.data( 'default', parseFloat( new_phone_default ) );
						$range_input.data( 'default', new_phone_default );
					}

					et_pb_check_range_boundaries( $this_el, range_input_value, true );
				} );
			}

			$range_input.on( 'keyup change', function( event ) {
				var $this_el      = $(this),
					this_device   = typeof $this_el.data( 'device' ) === 'undefined' ? 'all' : $this_el.data( 'device' ),
					this_value    = et_pb_get_range_input_value( $this_el, true ),
					$range_slider = 'all' === this_device ? $this_el.siblings( '.et-pb-range' ) : $this_el.siblings( '.et-pb-range.et_pb_setting_mobile_' + this_device ),
					update_step   = false,
					slider_value;

				slider_value = parseFloat( this_value ) || 0;

				// check the range slider step and update if value entered manually.
				if ( ! _.isUndefined( event.type ) && 'keyup' === event.type ) {
					update_step = true;
				}

				et_pb_check_range_boundaries( $range_slider, slider_value, update_step );

				$range_slider.val( slider_value ).trigger( 'et_pb_setting:change' );

				et_pb_update_mobile_defaults( $this_el );
			} );

			if ( $validate_unit_field.length ) {
				$validate_unit_field.each( function() {
					var $this_el = $(this),
						value    = et_pb_sanitize_input_unit_value( $.trim( $this_el.val() ) );

					$this_el.val( value );
				} );
			}

			if ( $advanced_tab_settings.length ) {
				$advanced_tab_settings.on( 'change et_pb_setting:change et_main_custom_margin:change', function() {
					var $this_el         = $(this),
						this_device       = typeof $this_el.data( 'device' ) === 'undefined' ? 'all' : $this_el.data( 'device' ),
						$option_container = $this_el.closest( '.et-pb-option-container' ),
						$reset_button    = $option_container.find( '.et-pb-reset-setting' ),
						is_range_option  = $this_el.hasClass( 'et-pb-range' ),
						$current_element = is_range_option && 'all' === this_device ? $this_el.siblings( '.et-pb-range-input' ) : $this_el,
						$current_element = is_range_option && 'all' !== this_device ? $this_el.siblings( '.et-pb-range-input.et_pb_setting_mobile_' + this_device ) : $current_element,
						default_value    = et_pb_get_default_setting_value( $current_element ),
						current_value    = $current_element.val(),
						$mobile_toggle   = $option_container.find( '.et-pb-mobile-settings-toggle' );

					if ( $current_element.hasClass( 'et_pb_setting_mobile' ) && ! $current_element.hasClass( 'et_pb_setting_mobile_active' ) ) {
						// make the mobile toggle icon visible if any option is not default
						if ( ( current_value !== default_value && ! is_range_option ) || ( is_range_option && current_value !== default_value + 'px' && current_value !== default_value ) ) {
							$mobile_toggle.addClass( 'et-pb-mobile-icon-visible' );
						}

						// do not proceed if mobile settings are not opened and we're processing mobile field
						return;
					}

					if ( $current_element.is( 'select' ) && default_value === '' && $current_element.prop( 'selectedIndex' ) === 0 ) {
						$reset_button.removeClass( 'et-pb-reset-icon-visible' );

						return;
					}

					// range option default value can be defined without units, so compare current value with default and default + 'px' for range option
					if ( ( current_value !== default_value && ! is_range_option ) || ( is_range_option && current_value !== default_value + 'px' && current_value !== default_value ) ) {
						setTimeout( function() {
							$reset_button.addClass( 'et-pb-reset-icon-visible' );
						}, 50 );

						$mobile_toggle.addClass( 'et-pb-mobile-icon-visible' );
					} else {
						$reset_button.removeClass( 'et-pb-reset-icon-visible' );
						if ( ! $mobile_toggle.hasClass( 'et-pb-mobile-settings-active' ) ) {
							$mobile_toggle.removeClass( 'et-pb-mobile-icon-visible' );
						}
					}
				} );

				$advanced_tab_settings.trigger( 'change' );

				$container.find( '.et-pb-main-settings .et_pb_options_tab_advanced a' ).append( '<span class="et-pb-reset-settings"></span>' );

				$container.find( '.et-pb-reset-settings' ).on( 'click', function() {
					et_pb_create_prompt_modal( 'reset_advanced_settings', $advanced_tab_settings );
				} );
			}

			$container.find( '.et-pb-reset-setting' ).on( 'click', function() {
				et_pb_reset_element_settings( $(this) );
			} );


			if ( $et_affect_fields.length ) {
				$et_affect_fields.change( function() {
					var $this_field         = $(this), // this field value affects another field visibility
						new_field_value     = $this_field.val(),
						new_field_value_number = parseInt( new_field_value ),
						data_affects_obj    = _.map( $this_field.data( 'affects' ).split(', '), function( affect ) {
							var is_selector = ( 'image' !== affect ) && $( affect ).length;

							return is_selector ? affect : '#et_pb_' + affect;
						} ),
						data_affects         = data_affects_obj.join(', '),
						$affected_fields     = $container.find( data_affects ),
						this_field_tab_index = $this_field.closest( '.et-pb-options-tab' ).index();

					$affected_fields.each( function() {
						var $affected_field          = $(this),
							$affected_container      = $affected_field.closest( '.et-pb-option' ),
							is_text_trigger          = 'text' === $this_field.attr( 'type' ) && typeof show_if_not === 'undefined' && typeof show_if === 'undefined', // need to know if trigger is text field
							show_if                  = $affected_container.data( 'depends_show_if' ) || 'on',
							show_if_not              = is_text_trigger ? '' : $affected_container.data( 'depends_show_if_not' ),
							show                     = show_if === new_field_value || ( typeof show_if_not !== 'undefined' && show_if_not !== new_field_value ),
							affected_field_tab_index = $affected_field.closest( '.et-pb-options-tab' ).index(),
							$dependant_fields        = $affected_container.find( '.et-pb-affects' ), // affected field might affect some other fields as well
							is_use_background_color_gradient = $this_field.closest( '.et_pb_background-option--use_background_color_gradient' ).length;

						// make sure hidden text fields do not break the visibility of option
						if ( is_text_trigger && ! $this_field.is( ':visible' ) ) {
							return;
						}

						// if the affected field should be displayed, but the field that affects it is not visible, don't show the affected field ( it only can happen on settings page load )
						if ( this_field_tab_index === affected_field_tab_index && show && ! $this_field.is( ':visible' ) && ! $this_field.is( '[type="hidden"]' ) && ! is_use_background_color_gradient ) {
							show = false;
						}

						// shows or hides the affected field container
						$affected_container.toggle( show ).addClass( 'et_pb_animate_affected' );

						var event = $affected_field.is( ':visible' ) ? 'et-pb-option-field-shown' : 'et-pb-option-field-hidden';
						$affected_field.trigger( event );

						setTimeout( function() {
							$affected_container.removeClass( 'et_pb_animate_affected' );
							et_pb_hide_empty_toggles( $affected_field );
						}, 500 );

						// if the affected field affects other fields, find out if we need to hide/show them
						if ( $dependant_fields.length ) {
								var data_inner_affects_obj = _.map( $dependant_fields.data( 'affects' ).split(', '), function( affect ) {
									var is_selector = ( 'image' !== affect ) && $( affect ).length;

									return is_selector ? affect : '#et_pb_' + affect;
								} );
								var data_inner_affects = data_inner_affects_obj.join(', ');
								var $inner_affected_elements = $( data_inner_affects );

							if ( ! $affected_container.is( ':visible' ) ) {
								// if the main affected field is hidden, hide all fields it affects

								$inner_affected_elements.each( function() {
									$(this).closest( '.et-pb-option' ).hide();
									et_pb_hide_empty_toggles( $(this) );
								} );
							} else {
								// if the main affected field is displayed, trigger the change event for all fields it affects

								$affected_field.trigger( 'change' );
							}
						}
					} );

					$('.et_options_list').each(function() {
						var $list = $(this);

						if ( 0 !== $list.find('.et_options_rows').length ) {
							return;
						}

						var $options = $list.find('textarea');

						$list.find('.et_options_list_row').wrapAll('<div class="et_options_rows" />');

						$list.find( '.et_options_rows' ).sortable({
							axis : 'y',
							containment: 'parent',
							update: function() {
								update_options_list( $list, $options );
								setTimeout( function() {
									$('.et_options_rows').children().css({
										position: '',
										top: '',
										left: ''
									});
								}, 700);
							}
						});
					});
				} );

				// trigger change event for all dependant ( affected ) fields to show on settings page load
				setTimeout( function() {
					// make all settings visible to properly enable all affected fields
					$settings_tab.css( { 'display' : 'block' } );

					et_pb_update_affected_fields( $et_affect_fields );

					// After all affected fields is being processed return all tabs to the initial state
					$settings_tab.css( { 'display' : 'none' } );
					et_pb_open_current_tab();

					// Remove all empty toggles which may appear in Section settings
					if ( $( '.et-pb-option-toggle-content' ).length > 0 ) {
						$( '.et-pb-option-toggle-content' ).each( function() {
							if ( '' === $.trim( $( this ).text() ) ) {
								$( this ).closest( '.et-pb-options-toggle-container' ).remove();
							}
						});
					}
				}, 100 );
			}

			// update the unique class for opened module when custom css tab opened
			$container.find( '.et-pb-options-tabs-links' ).on( 'et_pb_main_tab:changed', function() {
				var $custom_css_tab = $( '.et-pb-options-tabs-links' ).find( '.et_pb_options_tab_custom_css' ),
					$module_order_placeholder = $( '.et-pb-options-tab-custom_css' ).find( '.et_pb_module_order_placeholder' ),
					opened_module,
					module_order;

				if ( $custom_css_tab.hasClass( 'et-pb-options-tabs-links-active' ) ) {
					var opened_module = ET_PageBuilder_Modules.findWhere( { cid : this_module_cid } );

					module_order = typeof opened_module.attributes.module_order !== 'undefined' ? opened_module.attributes.module_order : '';

					// replace empty placeholders with module order value if any
					if ( $module_order_placeholder.length ) {
						$module_order_placeholder.replaceWith( module_order );
					}
				}
			});

			// show/hide css selector field for the custom css options on focus
			if ( $custom_css_option.length ) {
				$custom_css_option.focusin( function() {
					var $this = $( this ),
						$this_main_container = $this.closest( '.et-pb-option' ),
						$css_selector_holder = $this_main_container.find( 'label > span' ),
						$other_inputs_selectors = $this_main_container.siblings().find( 'label > span' );

					// show the css selector span for option with focus
					if ( $css_selector_holder.length ) {
						$css_selector_holder.removeClass( 'et_pb_hidden_css_selector' );
						$css_selector_holder.css( { 'display' : 'inline-block' } );
						$css_selector_holder.addClass( 'et_pb_visible_css_selector' );
					}

					// hide the css selector span for other options
					if ( $other_inputs_selectors.length ) {
						$other_inputs_selectors.removeClass( 'et_pb_visible_css_selector' );
						$other_inputs_selectors.addClass( 'et_pb_hidden_css_selector' );

						setTimeout( function() {
							$other_inputs_selectors.css( { 'display' : 'none' } );
							$other_inputs_selectors.removeClass( 'et_pb_hidden_css_selector' );
						}, 200 );
					}
				});
			}

			// handle global module options sync button clicks
			$container.on( 'click', '.et_pb_global_sync_switcher', function() {
				var $clicked_button = $( this );
				var option_unsynced = $clicked_button.hasClass( 'et_pb_global_unsynced' );

				if ( option_unsynced ) {
					$clicked_button.removeClass( 'et_pb_global_unsynced' );
				} else {
					$clicked_button.addClass( 'et_pb_global_unsynced' );
				}
			});
		}

		// check the range slider boundaries against the provided value and extend min or max boundary if needed
		function et_pb_check_range_boundaries( $range_slider, slider_value, update_step ) {
			var slider_max = parseFloat( $range_slider.attr( 'max' ) ),
				slider_min = parseFloat( $range_slider.attr( 'min' ) );

			if ( $range_slider.hasClass( 'et-pb-fixed-range' ) ) {
				return;
			}

			slider_value = '' !== slider_value ? parseFloat( slider_value ) : 0;

			// extend max boundary of the slider if needed
			if ( slider_value > slider_max ) {
				$range_slider.attr( 'max', slider_value );

				$range_slider.val(slider_value);
			}

			// extend min boundary of the slider if needed
			if ( slider_value < slider_min ) {
				$range_slider.attr( 'min', slider_value );

				$range_slider.val(slider_value);
			}

			// set the step to 0.1 if we need to update step and value is not integer
			if ( update_step && '0.1' !== $range_slider.attr( 'step' ) && ( slider_value % 1 > 0 ) ) {
				$range_slider.attr( 'step', '0.1' );
				// reset the slider value to make sure it updated correctly after changing the step
				$range_slider.val( slider_value );
			}
		}

		function et_pb_get_range_input_value( $range_input, update_element_value ) {
			var $range_field    = $range_input.parent().find( '.et-pb-range' ),
				range_value     = $range_input.val(),
				range_processed = typeof range_value === 'string' ? range_value.trim() : range_value,
				range_digit     = parseFloat( range_processed ),
				range_string    = range_processed.toString().replace( range_digit, '' ),
				result;

			// no need to use Number.isNaN there. parseFloat guarantees that we have a number or `NaN` value at this point.
			if ( isNaN( range_digit ) ) {
				range_digit = '';
			}

			if ( $range_field.hasClass( 'et-pb-fixed-range' ) ) {
				var range_field_max = parseFloat( $range_field.attr( 'max' ) ),
					range_field_min = parseFloat( $range_field.attr( 'min' ) ),
					is_too_high = range_digit > range_field_max,
					is_too_low = range_digit < range_field_min;

				if ( is_too_high ) {
					range_digit = range_field_max;
				} else if ( is_too_low ) {
					range_digit = range_field_min;
				}
			}

			result = range_digit.toString() + range_string;

			if ( update_element_value && result !== range_value ) {
				$range_input.val( result );
			}

			return result;
		}

		function et_pb_get_default_setting_value( $element ) {
			var default_data_name = $element.hasClass( 'et-pb-color-picker-hex' ) ? 'default-color' : 'default',
				default_value;

			// need to check for 'undefined' type instead of $element.data( default_data_name ) || '' because default value maybe 0
			default_value = typeof $element.data( default_data_name ) !== 'undefined' ? $element.data( default_data_name ) : '';
			// convert any type to string
			default_value = default_value + '';

			return default_value;
		}

		function et_pb_update_gradient_preview( $this ) {
			var $wrapper = $this.closest('.et_pb_background-tab--gradient'),
				gradient = et_pb_get_gradient( $wrapper ),
				$preview = $wrapper.find('.et-pb-option-preview');

			if ( gradient ) {
				$preview.css({
					backgroundImage: gradient
				});
			} else {
				$preview.removeAttr( 'style' );
			}
		}

		function et_pb_get_gradient( $wrapper ) {
			var $use_gradient_input     = $wrapper.find('.et_pb_background-option--use_background_color_gradient select'),
				$start_input            = $wrapper.find('.et_pb_background-option--background_color_gradient_start .et-pb-color-picker-hex'),
				$end_input              = $wrapper.find('.et_pb_background-option--background_color_gradient_end .et-pb-color-picker-hex'),
				$type_input             = $wrapper.find('.et_pb_background-option--background_color_gradient_type select'),
				$linear_direction_input = $wrapper.find('.et_pb_background-option--background_color_gradient_direction .et-pb-range-input'),
				$radial_direction_input = $wrapper.find('.et_pb_background-option--background_color_gradient_direction_radial select'),
				$start_position_input   = $wrapper.find('.et_pb_background-option--background_color_gradient_start_position .et-pb-range-input'),
				$end_position_input     = $wrapper.find('.et_pb_background-option--background_color_gradient_end_position .et-pb-range-input');

			if ( 'on' !== $use_gradient_input.val() ) {
				return false;
			}

			var args = {
				type:            'linear',
				direction:       '180deg',
				radialDirection: 'center',
				colorStart:      '#2b87da',
				colorEnd:        '#29c4a9',
				startPosition:   '0%',
				endPosition:     '100%'
			};

			_.each({
				type:            $type_input.val(),
				direction:       $linear_direction_input.val(),
				radialDirection: $radial_direction_input.val(),
				colorStart:      $start_input.val(),
				colorEnd:        $end_input.val(),
				startPosition:   $start_position_input.val(),
				endPosition:     $end_position_input.val()
			}, function(value, key) {
				if ('' !== value && ! _.isUndefined(value)) {
					args[key] = value;
				}
			})

			var direction     = args.type === 'linear' ? args.direction : ( 'circle at ' + args.radialDirection ),
				startPosition = et_pb_sanitize_input_unit_value(args.startPosition, undefined, '%'),
				endPosition   = et_pb_sanitize_input_unit_value(args.endPosition, undefined, '%');

			return args.type + '-gradient( ' + direction + ', ' + args.colorStart + ' ' + startPosition + ', ' + args.colorEnd + ' ' + endPosition + ' )';
		}

		/*
		 * Reset icon or a setting field can be used as $element
		 */
		function et_pb_reset_element_settings( $element ) {
			var $this_el          = $element,
				$option_container = $this_el.closest( '.et-pb-option-container' ),
				$main_container   = $option_container.closest( '.et-pb-option' ),
				this_device       = typeof $this_el.data( 'device' ) === 'undefined' || ! $main_container.hasClass( 'et_pb_has_mobile_settings' ) ? 'all' : $this_el.data( 'device' ),
				$main_setting     = 'all' === this_device ? $option_container.find( '.et-pb-main-setting' ) : $option_container.find( '.et-pb-main-setting.et_pb_setting_mobile_' + this_device ),
				default_value     = et_pb_get_default_setting_value( $main_setting );

			if ( $main_setting.is( 'select' ) && default_value === '' ) {
				$main_setting.prop( 'selectedIndex', 0 ).trigger( 'change' );

				return;
			}

			if ( $main_setting.hasClass( 'et-pb-custom-color-picker' ) ) {
				et_pb_custom_color_remove( $this_el );

				return;
			}

			if ( $main_setting.hasClass( 'et-pb-color-picker-hex' ) ) {
				$main_setting.wpColorPicker( 'color', default_value );

				if ( default_value === '' ) {
					$main_setting.siblings('.wp-picker-clear').trigger('click');
				}

				if ( ! $this_el.hasClass( 'et-pb-reset-setting' ) ) {
					$this_el = $option_container.find( '.et-pb-reset-setting' );
				}

				$this_el.hide();

				return;
			}

			if ( $main_setting.hasClass( 'et-pb-font-select' ) ) {
				et_pb_setup_font_setting( $main_setting, true );
			}

			if ( $main_setting.hasClass( 'et-pb-range' ) ) {
				$main_setting = 'all' === this_device ? $this_el.siblings( '.et-pb-range-input' ) : $this_el.siblings( '.et-pb-range-input.et_pb_setting_mobile_' + this_device );
				default_value = et_pb_get_default_setting_value( $main_setting );
			}

			$main_setting.val( default_value );

			$main_setting.data( 'has_saved_value', 'no' );

			if ( $main_setting.hasClass( 'et_custom_margin_main' ) ) {
				$main_setting.trigger( 'et_main_custom_margin:change' );
			} else {
				$main_setting.trigger( 'change' );
			}
		}

		function et_pb_sanitize_input_unit_value( value, auto_important, default_unit ) {
			var value = typeof value === 'undefined' ? '' : value,
				valid_one_char_units  = [ "%" ],
				valid_two_chars_units = [ "em", "px", "cm", "mm", "in", "pt", "pc", "ex", "vh", "vw" ],
				valid_three_chars_units = [ "deg" ],
				important             = "!important",
				important_length      = important.length,
				has_important         = false,
				value_length          = value.length,
				auto_important       = _.isUndefined( auto_important ) ? false : auto_important,
				unit_value,
				result;

			if ( value === '' ) {
				return '';
			}

			// check for !important
			if ( value.substr( ( 0 - important_length ), important_length ) === important ) {
				has_important = true;
				value_length = value_length - important_length;
				value = value.substr( 0, value_length ).trim();
			}

			if ( $.inArray( value.substr( -1, 1 ), valid_one_char_units ) !== -1 ) {
				unit_value = parseFloat( value ) + "%";

				// Re-add !important tag
				if ( has_important && ! auto_important ) {
					unit_value = unit_value + ' ' + important;
				}

				return unit_value;
			}

			if ( $.inArray( value.substr( -2, 2 ), valid_two_chars_units ) !== -1 ) {
				var unit_value = parseFloat( value ) + value.substr( -2, 2 );

				// Re-add !important tag
				if ( has_important && ! auto_important ) {
					unit_value = unit_value + ' ' + important;
				}

				return unit_value;
			}

			if ( $.inArray( value.substr( -3, 3 ), valid_three_chars_units ) !== -1 ) {
				var unit_value = parseFloat( value ) + value.substr( -3, 3 );

				// Re-add !important tag
				if ( has_important && ! auto_important ) {
					unit_value = unit_value + ' ' + important;
				}

				return unit_value;
			}

			if( isNaN( parseFloat( value ) ) ) {
				return '';
			}

			result = parseFloat( value );
			if ( _.isUndefined( default_unit ) || 'no_default_unit' !== default_unit ) {
				result += 'px';
			}

			// Return and automatically append px (default value)
			return result;
		}

		function et_pb_process_custom_margin_field( $element ) {
			var $this_field      = $element,
				this_device      = typeof $this_field.data( 'device' ) !== 'undefined' ? $this_field.data( 'device' ) : 'all',
				this_field_value = $this_field.val(),
				$container       = $this_field.closest( '.et_custom_margin_padding' ),
				$main_container  = $container.closest( '.et-pb-option-container' ),
				$mobile_toggle   = $main_container.find( '.et-pb-mobile-settings-toggle' ),
				$margin_fields   = 'all' === this_device ? $container.find( '.et_custom_margin' ) : $container.find( '.et_custom_margin.et_pb_setting_mobile_' + this_device ),
				show_mobile      = false,
				i = 0,
				margins;

			et_pb_update_mobile_defaults( $element );

			if ( this_field_value !== '' ) {
				margins = this_field_value.split( '|' );

				// if we have more fields than saved values, then add missing ones considering that saved values are top and bottom padding/margin
				if ( $margin_fields.length > margins.length ) {
					// fill the 2nd and 4th positions with empty values
					margins.splice( 1, 0, '' );
					margins.push( '' );
				}

				$margin_fields.each( function() {
					var $this_field = $(this),
						field_index = $margin_fields.index( $this_field ),
						auto_important  = $this_field.hasClass( 'auto_important' ),
						corner_value = et_pb_sanitize_input_unit_value( margins[ field_index ], auto_important );

					$this_field.val( corner_value );

					if ( '' !== corner_value ) {
						show_mobile = true;
					}
				} );

				if ( show_mobile ) {
					$mobile_toggle.addClass( 'et-pb-mobile-icon-visible' );
				}
			} else {
				$margin_fields.each( function() {
					$(this).val( '' );
				} );
			}
		}

		function et_pb_setup_font_setting( $element, reset ) {
			var $this_el           = $element,
				$container         = $this_el.parent('.et-pb-option-container'),
				$options_container = $this_el.closest( '.et-pb-options-tab' ),
				$main_option       = $container.find( 'input.et-pb-font-select' ),
				$select_option     = $container.find( 'select.et-pb-font-select' ),
				$style_options     = $container.find( '.et_builder_font_styles' ),
				$bold_option       = $style_options.find( '.et_builder_bold_font' ),
				$italic_option     = $style_options.find( '.et_builder_italic_font' ),
				$uppercase_option  = $style_options.find( '.et_builder_uppercase_font' ),
				$underline_option  = $style_options.find( '.et_builder_underline_font' ),
				style_active_class = 'et_font_style_active',
				font_value         = $.trim( $main_option.val() ),
				all_caps_option    = typeof $main_option.data( 'old-option-ref' ) !== 'undefined' && '' !== $main_option.data( 'old-option-ref' ) ? $main_option.data( 'old-option-ref' ) : '',
				$all_caps_el       = '' !== all_caps_option ? $options_container.find( '.et-pb-option-' + all_caps_option ) : '',
				$all_caps_input    = '' !== $all_caps_el && $all_caps_el.length ? $all_caps_el.find( 'input' ) : '',
				font_values;

			if ( reset ) {
				font_value = $.trim( $main_option.attr('data-default') );
			}

			if ( font_value !== '' ) {
				font_values = font_value.split( '|' );

				if ( font_values[0] !== '' ) {
					$select_option.val( font_values[0] );
				} else {
					$select_option.prop( 'selectedIndex', 0 );
				}

				if ( font_values[1] === 'on' ) {
					$bold_option.addClass( style_active_class );
				} else {
					$bold_option.removeClass( style_active_class );
				}

				if ( font_values[2] === 'on' ) {
					$italic_option.addClass( style_active_class );
				} else {
					$italic_option.removeClass( style_active_class );
				}

				if ( font_values[3] === 'on' ) {
					$uppercase_option.addClass( style_active_class );
				} else {
					$uppercase_option.removeClass( style_active_class );
				}

				if ( font_values[4] === 'on' ) {
					$underline_option.addClass( style_active_class );
				} else {
					$underline_option.removeClass( style_active_class );
				}
			} else {
				$select_option.prop( 'selectedIndex', 0 );
				$bold_option.removeClass( style_active_class );
				$italic_option.removeClass( style_active_class );
				$uppercase_option.removeClass( style_active_class );
				$underline_option.removeClass( style_active_class );
			}

			// backward compatibility for obsolete "all caps" option
			if ( $all_caps_input.length && '' !== $all_caps_input.val() ) {
				// turn on uppercase option if it's off, but "All Caps" is on
				if ( font_values[3] !== 'on' && 'on' === $all_caps_input.val() ) {
					$uppercase_option.addClass( style_active_class );
					font_values[3] = 'on';
					// update the value of font option if Uppercase value was changed
					$main_option.val( font_values.join( '|' ) ).trigger( 'change' );
				}

				// reset the value for obsolete "all caps" option to remove it from shortcode
				$all_caps_input.val( '' );
			}
		}

		function et_pb_hide_active_color_picker( container ) {
			container.$( '.et-pb-color-picker-hex:visible' ).each( function(){
				$(this).closest( '.wp-picker-container' ).find( '.wp-color-result' ).trigger( 'click' );
			} );
		}

		function et_builder_debug_message() {
			if ( et_pb_options.debug && window.console ) {
				if ( 2 === arguments.length ) {
					console.log( arguments[0], arguments[1] );
				} else {
					console.log( arguments[0] );
				}
			}
		}

		function et_reinitialize_builder_layout( update_global_modules ) {
			ET_PageBuilder_App.saveAsShortcode();

			setTimeout( function(){
				var $builder_container = $( '#et_pb_layout' ),
					builder_height     = $builder_container.innerHeight();

				$builder_container.css( { 'height' : builder_height } );

				content = et_pb_get_content( 'content', true );

				ET_PageBuilder_App.removeAllSections();

				ET_PageBuilder_App.$el.find( '.et_pb_section' ).remove();

				ET_PageBuilder_App.createLayoutFromContent( et_prepare_template_content( content ), '', '', { is_reinit : 'reinit' } );

				$builder_container.css( { 'height' : 'auto' } );

				ET_PageBuilder_AB_Testing.update();

				// in some cases we may need to update the content of global Rows and Section right after the layout reinit
				if ( ! update_global_modules ) {
					return;
				}

				// get array of global Rows and Sections on the page
				var global_modules = _.filter( ET_PageBuilder_Modules.models, function( model ) {
					return 'module' !== model.attributes.type && typeof model.attributes.et_pb_global_module !== 'undefined' && model.attributes.et_pb_global_module !== '';
				} );

				// update saved global template for each Section and Row
				if ( 0 !== global_modules.length ) {
					_.each( global_modules, function( model ) {
						et_pb_update_global_template( model.attributes.cid );
					});
				}
			}, 600 );
		}

		// TODO, need to put WP credits here, there is code in this function thats is based on/from WP core heartbeat.js
		// wp-includes/js/heartbeat.js
		function et_builder_check_for_frontend_builder_updates(){
			var $last_post_modified = $('#et_pb_last_post_modified');
			var last_post_modified  = $last_post_modified.val();
			var post_id = $('#post_ID').val();
			var blog_id = typeof window.autosaveL10n !== 'undefined' && window.autosaveL10n.blog_id;
			var force_check = false;
			var latest_post_content;
			var is_builder_used;
			var hidden;
			var visibilityState;
			var visibilitychange;
			var blurred;
			var focused;
			var checkFocusTimer;
			var checkFocus;
			var hasFocus = true;
			var force_autosave = false;
			var secure = ( 'https:' === window.location.protocol );
			var wp_saved_cookie = wpCookies.get( 'wp-saving-post' );
			var hasStorage;
			var autosaving_in_progress_cookie;
			var cookie_expires = 5 * 60; // 5 minutes
			var is_reloading = false;

			// Check if the browser supports sessionStorage and it's not disabled
			var checkStorage = function() {
				var test = Math.random().toString(),
					result = false;

				try {
					window.sessionStorage.setItem( 'wp-test', test );
					result = window.sessionStorage.getItem( 'wp-test' ) === test;
					window.sessionStorage.removeItem( 'wp-test' );
				} catch(e) {}

				hasStorage = result;
				return result;
			};
			checkStorage();

			/**
			 * Initialize the local storage
			 *
			 * @return mixed False if no sessionStorage in the browser or an Object containing all postData for this blog
			 */
			var getStorage = function(key_prefix) {
				var stored_obj = false;
				key_prefix = key_prefix || 'wp';
				// Separate local storage containers for each blog_id
				if ( hasStorage && blog_id ) {
					stored_obj = sessionStorage.getItem( key_prefix + '-autosave-' + blog_id );

					if ( stored_obj ) {
						stored_obj = JSON.parse( stored_obj );
					} else {
						stored_obj = {};
					}
				}

				return stored_obj;
			};

			/**
			 * Set the storage for this blog
			 *
			 * Confirms that the data was saved successfully.
			 *
			 * @return bool
			 */
			var setStorage = function( stored_obj, key_prefix ) {
				var key;
				key_prefix = key_prefix || 'wp';

				if ( hasStorage && blog_id ) {
					key = key_prefix + '-autosave-' + blog_id;
					sessionStorage.setItem( key, JSON.stringify( stored_obj ) );
					return sessionStorage.getItem( key ) !== null;
				}

				return false;
			};

			/**
			 * Set (save or delete) post data in the storage.
			 *
			 * If stored_data evaluates to 'false' the storage key for the current post will be removed
			 *
			 * $param stored_data The post data to store or null/false/empty to delete the key
			 * @return bool
			 */
			var setData = function( stored_data, key_prefix ) {
				var stored = getStorage(key_prefix);

				if ( ! stored || ! post_id ) {
					return false;
				}

				if ( stored_data ) {
					stored[ 'post_' + post_id ] = stored_data;
				} else if ( stored.hasOwnProperty( 'post_' + post_id ) ) {
					delete stored[ 'post_' + post_id ];
				} else {
					return false;
				}

				return setStorage( stored, key_prefix );
			};

			if ( wp_saved_cookie === post_id + '-saved' ) {
				// The post was saved properly, set cookie to remove old fb autosave data (if present)
				// FB will pick this up and clear its autosave
				wpCookies.set( 'et-saved-post-' + post_id + '-bb', 'bb', cookie_expires, et_pb_options.cookie_path, false, secure );
				wpCookies.set( 'et-recommend-sync-post-' + post_id + '-bb', 'bb', 30, et_pb_options.cookie_path, false, secure );
			}

			wpCookies.set( 'et-editor-available-post-' + post_id + '-bb', 'bb', cookie_expires * 6, et_pb_options.cookie_path, false, secure );

			function check_fb_saved() {
				var fb_saved_cookie = wpCookies.get( 'et-saved-post-' + post_id + '-fb' );
				if ( fb_saved_cookie ) {
					// The post was saved properly in FB, set cookie to remove old fb autosave data (if present)
					// FB will pick this up and clear its autosave
					setData(false, 'wp');

					var fb_editing_cookie = wpCookies.get( 'et-editing-post-' + post_id + '-fb' );
					if ( ! fb_editing_cookie ) {
						wpCookies.remove( 'et-saved-post-' + post_id + '-fb', et_pb_options.cookie_path, false, secure );
					}
				}
			}

			check_fb_saved();

			is_builder_used = function(){
				return $( '#et_pb_use_builder' ).val() === 'on';
			};

			autosaving_in_progress_cookie = function(in_progress) {
				if ( in_progress ) {
					wpCookies.remove( 'et-saved-post-' + post_id + '-bb', et_pb_options.cookie_path, false, secure );
					wpCookies.set( 'et-saving-post-' + post_id + '-bb', 'bb', cookie_expires, et_pb_options.cookie_path, false, secure );
				} else {
					// remove "saving" and set "saved", both are looked for in FB
					wpCookies.remove( 'et-saving-post-' + post_id + '-bb', et_pb_options.cookie_path, false, secure );
					wpCookies.set( 'et-saved-post-' + post_id + '-bb', 'bb', cookie_expires, et_pb_options.cookie_path, false, secure );
				}
			};

			// Used to check if content has changed, to be used before forcing a autosave heartbeat
			var initialCompareString = _.isUndefined( wp.autosave ) ? '' : wp.autosave.getCompareString();
			var lastCompareString;
			var hasContentChanged = function() {
				if ( _.isUndefined( wp.heartbeat ) || _.isUndefined( wp.autosave ) ) {
					return false;
				}

				var postData = wp.autosave.getPostData('local');

				var compareString = wp.autosave.getCompareString( postData );

				if ( typeof lastCompareString === 'undefined' ) {
					lastCompareString = initialCompareString;
				}

				// If the content, title and excerpt did not change since the last save, don't save again
				if ( compareString === lastCompareString ) {
					return false;
				}

				lastCompareString = compareString;

				return true;
			};

			blurred = function(){
				if ( !is_builder_used() ) {
					return;
				}

				hasFocus = false;

				var cookie = wpCookies.get( 'et-editing-post-' + post_id + '-bb' );
				if ( cookie ) {

					// if content has not changed, no need to force an autosave heartbeat
					if ( !hasContentChanged() ) {
						return false;
					}

					// fire after next js event loop tick, so that during reload this will not even fire
					setTimeout(function(){
						// this flag is set, adding redundency to preventing autosave during reload
						if ( !is_reloading ) {
							force_autosave = true;
							autosaving_in_progress_cookie(true);

							if ( ! _.isUndefined( wp.heartbeat ) && ! _.isUndefined( wp.autosave ) ) {
								wp.autosave.server.triggerSave();
							}
						}
					}, 0);
				}
			};

			focused = function(){
				if ( !is_builder_used() ) {
					return false;
				}
				var post_id = $('#post_ID').val();
				var secure = ( 'https:' === window.location.protocol );
				var cookie_expires = 5 * 60; // 5 mins (in secs)

				hasFocus = true;

				var cookie = wpCookies.get( 'et-editing-post-' + post_id + '-fb' );
				if ( !cookie ) {
					return false;
				}

				if ( _.isUndefined( wp.heartbeat ) || _.isUndefined( wp.autosave ) ) {
					return false;
				}

				var syncMaxChecks = 5;
				var syncChecks = 0;
				var syncCheck = function(){
					var syncingCookieFB = wpCookies.get( 'et-syncing-post-' + post_id + '-fb' );
					var syncingCookieBB = wpCookies.get( 'et-syncing-post-' + post_id + '-bb' );

					var syncCheckedMax = syncChecks >= syncMaxChecks;
					if ( !syncCheckedMax && ( syncingCookieFB || syncingCookieBB ) ) {
						// bump loading state
						ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );
					} else {
						// remove loading state
						ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );
						return false;
					}

					syncChecks++;

					// check again in 1 sec (recursive)
					setTimeout(syncCheck, 1000);
					return true;
				};

				if (syncCheck()) {
					return;
				}

				wpCookies.set( 'et-syncing-post-' + post_id + '-bb', 'bb', 30, et_pb_options.cookie_path, false, secure );

				// external editor was in use!
				et_builder_debug_message('external editor was in use!');

				et_builder_debug_message('trigger preloader');
				// add loading state
				ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

				// temp block autosave
				wp.autosave.server.tempBlockSave();

				var FBDoneFoundSavingCookie = false;
				var FBDoneReCheckTimeout = 500;
				var FBDoneMaxWaitTime = 15000;
				var FBDoneMaxChecks = FBDoneMaxWaitTime / FBDoneReCheckTimeout;
				var FBDoneChecks = 0;
				var checkFBDoneAutosaving = function() {
					var FBsaved = false;

					// see if saved has happened
					var savedCookie = wpCookies.get( 'et-saved-post-' + post_id + '-fb' );
					if ( savedCookie ) {
						FBsaved = true;
					}

					if ( FBDoneChecks > FBDoneMaxChecks ) {
						FBsaved = true;
					}

					FBDoneChecks++;

					if ( _.isUndefined( wp.heartbeat ) || _.isUndefined( wp.autosave ) ) {
						return;
					}

					// bump temp block autosave
					wp.autosave.server.tempBlockSave();

					// bump loading state
					ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

					if ( FBsaved ) {
						et_builder_debug_message('calling wp.heartbeat.connectNow()');

						force_check = true;
						// check if FB saved, and if it did, clear our local autosave data
						check_fb_saved();

						// wait .5 sec so autosave has time to settle...?
						setTimeout(function(){
							wp.heartbeat.connectNow();
						}, 500);

						// clean these up, since we have consumed them
						wpCookies.remove( 'et-saving-post-' + post_id + '-fb', et_pb_options.cookie_path, false, secure );
						wpCookies.remove( 'et-saved-post-' + post_id + '-fb', et_pb_options.cookie_path, false, secure );
						wpCookies.remove( 'et-syncing-post-' + post_id + '-bb', et_pb_options.cookie_path, false, secure );
						// delete the fb-editing cookie, since the baton is in BB's hand now
						wpCookies.remove( 'et-editing-post-' + post_id + '-fb', et_pb_options.cookie_path, false, secure );
						return true;
					}

					// check again in .5 sec (recursive)
					setTimeout(checkFBDoneAutosaving, FBDoneReCheckTimeout);
				}

				// kick it off
				checkFBDoneAutosaving();
			};

			checkFocus = function() {
				if ( hasFocus && ! document.hasFocus() ) {
					blurred();
				} else if ( ! hasFocus && document.hasFocus() ) {
					focused();
				}
			};

			if ( typeof document.hidden !== 'undefined' ) {
				hidden = 'hidden';
				visibilitychange = 'visibilitychange';
				visibilityState = 'visibilityState';
			} else if ( typeof document.msHidden !== 'undefined' ) { // IE10
				hidden = 'msHidden';
				visibilitychange = 'msvisibilitychange';
				visibilityState = 'msVisibilityState';
			} else if ( typeof document.webkitHidden !== 'undefined' ) { // Android
				hidden = 'webkitHidden';
				visibilitychange = 'webkitvisibilitychange';
				visibilityState = 'webkitVisibilityState';
			}

			window.addEventListener("beforeunload", function( event ) {
				is_reloading = true;
				wpCookies.remove( 'et-editing-post-' + post_id + '-bb', et_pb_options.cookie_path, false, secure );
				wpCookies.remove( 'et-syncing-post-' + post_id + '-bb', et_pb_options.cookie_path, false, secure );
				wpCookies.remove( 'et-saving-post-' + post_id + '-bb', et_pb_options.cookie_path, false, secure );
				wpCookies.remove( 'et-editor-available-post-' + post_id + '-bb', et_pb_options.cookie_path, false, secure );
			});

			if ( hidden ) {
				$(document).on( visibilitychange + '.fb-heartbeat', function(e) {
					if ( !is_builder_used() ) {
						return;
					}
					if ( document[visibilityState] === 'hidden' ) {
						blurred();
						window.clearInterval( checkFocusTimer );
					} else {
						focused();
						if ( document.hasFocus ) {
							checkFocusTimer = window.setInterval( checkFocus, 1000 );
						}
					}
				});
			}

			// Use document.hasFocus() if available.
			if ( document.hasFocus ) {
				checkFocusTimer = window.setInterval( checkFocus, 1000 );
			}

			// fire it right away
			focused();

			// check for save from FB, even if it refreshes right after saving
			setInterval( function(){
				var fb_recommend_sync_cookie = wpCookies.get( 'et-recommend-sync-post-' + post_id + '-fb' );
				var fb_saved_cookie = wpCookies.get( 'et-saved-post-' + post_id + '-fb' );
				if ( fb_recommend_sync_cookie && fb_saved_cookie ) {
					if ( ! _.isUndefined( wp.heartbeat ) ) {
						wp.heartbeat.connectNow();
					}
					wpCookies.remove( 'et-recommend-sync-post-' + post_id + '-fb', et_pb_options.cookie_path, false, secure );
				}
			}, 3000 );

			// send last_post_modified with the heartbeatData sent to server
			// php et_pb_heartbeat_post_modified() hook is looking for this key
			$(document).on('heartbeat-send.bb-heartbeat', function(event, heartbeatData ){
				if ( !is_builder_used() ) {
					return;
				}

				heartbeatData.et = {
					last_post_modified: last_post_modified,
					built_by: 'bb',
					post_id: post_id,
					force_check: force_check,
					force_autosave: force_check ? false : force_autosave // if force_check, dont let it be force_autosave at same time
				};

				// reset force check after its used
				force_check = false;

				// set the autosave payload if its not there, since we are trying to force_autosave now
				if ( force_autosave && 'undefined' === typeof heartbeatData.wp_autosave ) {
					heartbeatData.wp_autosave = wp.autosave.getPostData();

					// Overwriting autosave's post data with the newest content change
					heartbeatData.wp_autosave.content = et_pb_get_content( 'content' );
					heartbeatData.wp_autosave._wpnonce = $( '#_wpnonce' ).val() || '';
				}

				if ( typeof heartbeatData.wp_autosave === 'object' ) {
					heartbeatData.wp_autosave.builder_settings = _.mapObject( _.indexBy( $( '.et_pb_page_settings input.et_pb_value_updated' ).serializeArray(), 'name' ), function( setting, name ) {
						return setting.value;
					} );
					heartbeatData.wp_autosave.et_fb_autosave_nonce = et_pb_options.et_fb_autosave_nonce;

					// Remove updated marker
					$( '.et_pb_page_settings input.et_pb_value_updated' ).removeClass('et_pb_value_updated');
				}

				// reset this flag after its used on the autosave heartbeat
				if ( force_autosave && 'undefined' !== typeof heartbeatData.wp_autosave ) {
					force_autosave = false;
				}
			});

			// check recieved heartbeat response, to see if et_pb_heartbeat_post_modified() determined
			// the post has been modified externally of this editor
			$(document).on('heartbeat-tick.bb-heartbeat', function(event, response ){
				if ( !is_builder_used() ) {
					return;
				}

				if ( ! _.isEmpty( response.et ) && ! _.isEmpty( response.et.builder_settings_autosave ) ) {
					var is_builder_settings_popup_opened = $( '.et_pb_modal_overlay.et_pb_builder_settings' ).length;

					_.each( response.et.builder_settings_autosave, function( value, name ) {
						$('#_' + name).val( value );

						if ( 'et_pb_section_background_color' === name ) {
							et_pb_options.page_section_bg_color = value;
						}

						if ( 'et_pb_page_gutter_width' === name ) {
							et_pb_options.page_gutter_width = value;
						}

						if ( 'et_pb_color_palette' === name ) {
							et_pb_options.page_color_palette = value;
						}

						// Live refresh for currently opened builder settings
						if ( is_builder_settings_popup_opened ) {
							var $field = $( '.et_pb_modal_overlay.et_pb_builder_settings div[data-id="'+ name +'"]' );
							var dataType = $field.attr( 'data-type');

							switch( dataType ) {
								case 'range':
									$field.find( '.range, .et-pb-range-input' ).val( value );
									break;
								case 'color-alpha':
									$field.find( '.input-colorpicker' ).wpColorPicker( 'color', value );
									break;
								case 'textarea':
									$field.find( 'textarea' ).val( value );
									break
								case 'colorpalette':
									var paletteColors = value.split('|');
									$field.find( '.input-colorpalette-colorpicker' ).each(function( index, palette ) {
										if ( ! _.isUndefined( paletteColors[index] ) ) {
											var paletteColor = paletteColors[index];
											$(palette).val( paletteColor ).wpColorPicker( 'color', paletteColor );
											$field.find( '.colorpalette-item-' + ( index + 1) ).css({
												'backgroundColor' : paletteColor
											});
										}
									});
									break;
								case 'yes_no_button':
									var $yn_wrapper = $field.find( '.et_pb_yes_no_button_wrapper' ),
										$yn_button  = $field.find( '.et_pb_yes_no_button' ),
										$yn_select  = $field.find( 'select' );

									$yn_select.find('option[value="'+value+'"]').prop('selected', true);

									if ( value === 'on' ) {
										$yn_button.removeClass( 'et_pb_off_state' ).addClass( 'et_pb_on_state' );
									} else {
										$yn_button.removeClass( 'et_pb_on_state' ).addClass( 'et_pb_off_state' );
									}
									break;
								default :
									$field.find( 'input' ).val( value );
									break;
							}
						}
					} );
				}

				// if this was a completed autosave, update our saving/saved cookie state
				if ( response.wp_autosave ) {
					autosaving_in_progress_cookie(false);
				}

				if ( 'undefined' === typeof response.et ) {
					return false;
				}

				if ( 'undefined' !== typeof response.et.post_modified ) {
					last_post_modified = response.et.post_modified;
				}

				if ( 'undefined' !== typeof response.et.post_content ) {
					et_builder_debug_message('ext changes occured');
					latest_post_content = response.et.post_content;

					// Close currently opened settings modal
					var $modalSettingsContainer = $('.et_pb_modal_settings_container');

					if ( $modalSettingsContainer.length && $modalSettingsContainer.attr('data-open_view') === 'module_settings' ) {
						$modalSettingsContainer.find('.et-pb-modal-close').trigger('click');
					}

					update_builder_content();
				} else {
					// remove loading state
					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );
				}
			});

			var update_builder_content = function(){
				$last_post_modified.val(last_post_modified);

				if ( !is_builder_used() ) {
					return;
				}

				// If this is a fresh new post/page, the first attempt will be to update with blank content from db (since its not saved yet in db)
				// so this check will skip that initial attempt
				if ( '' == latest_post_content ) {
					return;
				}

				// add loading state
				ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );

				// Set shortcode to editor
				var shortcode = latest_post_content;
				et_pb_set_content( 'content', shortcode, 'updating_to_latest_fb_content' );

				// Rebuild the builder
				setTimeout( function(){
					var $builder_container = $( '#et_pb_layout' ),
						builder_height     = $builder_container.innerHeight();

					$builder_container.css( { 'height' : builder_height } );

					ET_PageBuilder_App.removeAllSections();

					ET_PageBuilder_App.$el.find( '.et_pb_section' ).remove();

					// Ensure that no history is added for rollback
					ET_PageBuilder_App.enable_history = false;

					ET_PageBuilder_App.createLayoutFromContent( et_prepare_template_content( shortcode ), '', '', { is_reinit : 'reinit' } );

					// Auto turn on Split Testing if the history has Split testing data
					if ( ET_PageBuilder_AB_Testing.is_active_based_on_models() ) {
						ET_PageBuilder_AB_Testing.toggle_status( true );

						et_reinitialize_builder_layout();
					} else {
						ET_PageBuilder_AB_Testing.toggle_status( false );
					}

					$builder_container.css( { 'height' : 'auto' } );

					// remove loading state
					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );

					// delete the editing cookie since the above doesnt count as dirty'ing the content
					setTimeout(function(){
						wpCookies.remove( 'et-editing-post-' + post_id + '-bb', et_pb_options.cookie_path, false, secure );
					}, 500 );
				}, 500 );
			};
		}
		et_builder_check_for_frontend_builder_updates();

		$( document ).on( 'tinymce-editor-init.autosave', function( event, editor ) {
			if ( 'content' === editor.id ) {
				$( document ).off( 'tinymce-editor-init.autosave' );
			}
		});

		function et_prepare_template_content( content ) {
			if ( -1 !== content.indexOf( '[et_pb_' ) ) {
				if  ( -1 === content.indexOf( 'et_pb_row' ) && -1 === content.indexOf( 'et_pb_section' ) ) {
					if ( -1 === content.indexOf( 'et_pb_fullwidth' ) ) {
						content = '[et_pb_section template_type="module" skip_module="true"][et_pb_row template_type="module" skip_module="true"][et_pb_column type="4_4"]' + content + '[/et_pb_column][/et_pb_row][/et_pb_section]';
					} else {
						content = '[et_pb_section fullwidth="on" template_type="module" skip_module="true"]' + content + '[/et_pb_section]';
					}
				} else if ( -1 === content.indexOf( 'et_pb_section' ) ) {
					content = '[et_pb_section template_type="row" skip_module="true"]' + content + '[/et_pb_section]';
				}
			}

			return content;
		}

		function generate_templates_view( include_global, is_global, layout_type, append_to, module_width, specialty_cols, selected_category, previous_result ) {
			var is_global = '' === is_global ? 'not_global' : is_global;
			if ( typeof $et_pb_templates_cache[layout_type + '_' + is_global + '_' + module_width + '_' + specialty_cols] !== 'undefined' ) {
				var templates_collection = new ET_PageBuilder.SavedTemplates( $et_pb_templates_cache[layout_type + '_' + is_global + '_' + module_width + '_' + specialty_cols] ),
					templates_view = new ET_PageBuilder.TemplatesView( { collection: templates_collection, category: selected_category } );

				append_to.append( templates_view.render().el );

				if ( 'include_global' === include_global && 'not_global' === is_global ) {
					generate_templates_view( 'include_global', 'global', layout_type, append_to, module_width, specialty_cols, selected_category );
				} else {
					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );
					append_to.prepend( et_pb_generate_layouts_filter( selected_category ) );
					$( '#et_pb_select_category' ).data( 'attr', { include_global : include_global, is_global : '', layout_type : layout_type, append_to : append_to, module_width : module_width, specialty_cols : specialty_cols } );
				}
			} else {
				$.ajax( {
					type: "POST",
					url: et_pb_options.ajaxurl,
					dataType: 'json',
					data:
					{
						action : 'et_pb_get_saved_templates',
						et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
						et_is_global : is_global,
						et_post_type : et_pb_options.post_type,
						et_layout_type : layout_type,
						et_module_width : module_width,
						et_specialty_columns : specialty_cols
					},
					beforeSend : function() {
						ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );
					},
					complete : function() {
						if ( 'include_global' !== include_global || ( 'include_global' === include_global && 'global' === is_global )  ) {
							ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );
							append_to.prepend( et_pb_generate_layouts_filter( selected_category ) );
							$( '#et_pb_select_category' ).data( 'attr', { include_global : include_global, is_global : '', layout_type : layout_type, append_to : append_to, module_width : module_width, specialty_cols : specialty_cols } );
						}
					},
					success: function( data ) {
						var request_result = '';

						if ( typeof data.error !== 'undefined' ) {
							//show error message only for global section or when global section wasn't included
							if ( ( 'include_global' === include_global && 'global' === is_global && 'success' !== previous_result ) || 'include_global' !== include_global ) {
								append_to.append( '<ul><li>' + data.error + '</li></ul>');
								request_result = 'fail';
							}
						} else {
							var templates_collection = new ET_PageBuilder.SavedTemplates( data ),
								templates_view = new ET_PageBuilder.TemplatesView( { collection: templates_collection } );

							$et_pb_templates_cache[layout_type + '_' + is_global + '_' + module_width + '_' + specialty_cols] = data;
							append_to.append( templates_view.render().el );
							request_result = 'success';
						}

						if ( 'include_global' === include_global && 'not_global' === is_global ) {
							generate_templates_view( 'include_global', 'global', layout_type, append_to, module_width, specialty_cols, selected_category, request_result );
						}
					}
				} );
			}
		}

		function et_pb_generate_layouts_filter( selected_category ) {
			var all_cats        = $.parseJSON( et_pb_options.layout_categories ),
				$cats_selector  = '<select id="et_pb_select_category">',
				selected_option = 'all' === selected_category || '' === selected_category ? ' selected' : '';

				$cats_selector += '<option value="all"' + selected_option + '>' + et_pb_options.all_cat_text + '</option>';

				if( ! $.isEmptyObject( all_cats ) ) {

					$.each( all_cats, function( i, single_cat ) {
						if ( ! $.isEmptyObject( single_cat ) ) {
							selected_option = selected_category === single_cat.slug ? ' selected' : '';
							$cats_selector += '<option value="' + single_cat.slug + '"' + selected_option + '>' + single_cat.name + '</option>';
						}
					});
				}

				$cats_selector += '</select>';

				return $cats_selector;
		}

		// function to load saved layouts, it works differently than loading saved rows, sections and modules, so we need a separate function
		function et_load_saved_layouts( layout_type, container_class, $this_el, post_type, update_cache ) {
			if ( typeof $et_pb_templates_cache[layout_type + '_layouts'] !== 'undefined' && ! update_cache ) {
				$this_el.find( '.et-pb-main-settings.' + container_class ).append( $et_pb_templates_cache[layout_type + '_layouts'] );
			} else {
				$.ajax( {
					type: "POST",
					url: et_pb_options.ajaxurl,
					data:
					{
						action : 'et_pb_show_all_layouts',
						et_layouts_built_for_post_type: post_type,
						et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
						et_load_layouts_type : layout_type //'predefined' or not predefined
					},
					beforeSend : function() {
						if ( ! update_cache ) {
							ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );
						}
					},
					complete : function() {
						if ( ! update_cache ) {
							ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );
						}
					},
					success: function( data ){
						if ( ! update_cache ) {
							$this_el.find( '.et-pb-main-settings.' + container_class ).append( data );
						}
						$et_pb_templates_cache[layout_type + '_layouts'] = data;
					}
				} );
			}
		}

		function et_handle_templates_switching( $clicked_button, module_type, module_width ) {
			if ( ! $clicked_button.hasClass( 'et-pb-options-tabs-links-active' ) ) {
				var specialty_columns = typeof $clicked_button.closest( '.et-pb-options-tabs-links' ).data( 'specialty_columns' ) !== 'undefined' ? $clicked_button.closest( '.et-pb-options-tabs-links' ).data( 'specialty_columns' ) : 0;
				$( '.et-pb-options-tabs-links li' ).removeClass( 'et-pb-options-tabs-links-active' );
				$clicked_button.addClass( 'et-pb-options-tabs-links-active' );

				$( '.et-pb-main-settings.active-container' ).css( { 'display' : 'block', 'opacity' : 1 } ).stop( true, true ).animate( { opacity : 0 }, 300, function(){
					$( this ).css( 'display', 'none' );
					$( this ).removeClass( 'active-container' );
					$( '.' + $clicked_button.data( 'open_tab' ) ).addClass( 'active-container' ).css( { 'display' : 'block', 'opacity' : 0 } ).stop( true, true ).animate( { opacity : 1 }, 300 );
				});

				if ( typeof $clicked_button.data( 'content_loaded' ) === 'undefined' && ! $clicked_button.hasClass( 'et-pb-new-module' ) && 'layout' !== module_type ) {
					var include_global = $clicked_button.closest( '.et_pb_modal_settings' ).hasClass( 'et_pb_no_global' ) ? 'no_global' : 'include_global';
					generate_templates_view( include_global, '', module_type, $( '.' + $clicked_button.data( 'open_tab' ) ), module_width, specialty_columns, 'all' );
					$clicked_button.data( 'content_loaded', 'true' );
				}
			}
		}

		function et_pb_maybe_apply_wpautop_to_models( editor_mode, load ) {
			if ( typeof window.switchEditors === 'undefined' ) {
				return;
			}

			var tinymce_advanced_noautop = tinyMCEPreInit.mceInit.et_pb_content_new.tadv_noautop; // get the noautop option from tinyMCE advanced plugin

			_.each( ET_PageBuilder_App.collection.models, function( model ) {
				var model_content = model.get( 'et_pb_content_new' );

				if ( typeof model_content !== 'undefined' ) {
					if ( editor_mode === 'tinymce' ) {
						model_content = window.switchEditors.wpautop( model_content.replace( /<p> <\/p>/g, "<p>&nbsp;</p>" ) );
					} else {
						// do not remove the <p> and <br /> tags in the Text editor, if such option is enabled in TinyMCE Advanced Plugin
						if ( typeof tinymce_advanced_noautop !== 'undefined' && tinymce_advanced_noautop === true ) {
							return;
						}

						// do not remove <br /> tags on initial page load
						if ( ! _.isUndefined( load ) && load === 'initial_load' ) {
							return;
						}

						model_content = window.switchEditors.pre_wpautop( model_content );
					}

					model.set( 'et_pb_content_new', model_content, { silent : true } );
				}
			} );
		}

		function et_add_template_meta( custom_field_name, value ) {
			var current_post_id = et_pb_options.template_post_id;
			$.ajax( {
					type: "POST",
					url: et_pb_options.ajaxurl,
					data:
					{
						action : 'et_pb_add_template_meta',
						et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
						et_meta_value : value,
						et_custom_field : custom_field_name,
						et_post_id : current_post_id
					}
			} );
		}

		function et_builder_get_global_module( view_settings ) {
			var modal_view,
				shortcode_atts,
				global_module_id = view_settings.model.get( 'et_pb_global_module' );

			$.ajax( {
				type: "POST",
				url: et_pb_options.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'et_pb_get_global_module',
					et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
					et_global_id : global_module_id
				},
				beforeSend : function() {
					ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );
				},
				complete : function() {
					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );
				},
				success: function( data ) {
					if ( data.error ) {
						// if global template not found, then make module not global.
						view_settings.model.unset( 'et_pb_global_module' );
						view_settings.model.unset( 'et_pb_saved_tabs' );
					} else {
						var et_pb_shortcodes_tags = ET_PageBuilder_App.getShortCodeParentTags(),
							reg_exp = window.wp.shortcode.regexp( et_pb_shortcodes_tags ),
							inner_reg_exp = ET_PageBuilder_App.wp_regexp_not_global( et_pb_shortcodes_tags ),
							matches = data.shortcode.match( reg_exp ),
							selective_sync_method = data.sync_status,
							unsynced_options = 'updated' === selective_sync_method ? JSON.parse( data.excluded_options ) : [];
						if ( 'updated' === selective_sync_method ) {
							et_pb_all_unsynced_options[ global_module_id ] = 0 < unsynced_options.length ? unsynced_options : [];
						} else {
							et_pb_all_legacy_synced_options[ global_module_id ] = [];
						}

						_.each( matches, function ( shortcode ) {
							var shortcode_element = shortcode.match( inner_reg_exp ),
								shortcode_name = shortcode_element[2],
								shortcode_attributes = shortcode_element[3] !== ''
									? window.wp.shortcode.attrs( shortcode_element[3] )
									: '',
								shortcode_content = shortcode_element[5],
								module_settings,
								found_inner_shortcodes = typeof shortcode_content !== 'undefined' && shortcode_content !== '' && shortcode_content.match( reg_exp ),
								saved_tabs = shortcode_attributes['named']['saved_tabs'] || view_settings.model.get('et_pb_saved_tabs'),
								ignore_admin_label = 'updated' !== selective_sync_method && 'all' !== saved_tabs && -1 === saved_tabs.indexOf( 'general' ), // we should load Admin Label only if General tab is synced
								sync_content = 'updated' === selective_sync_method ? -1 === et_pb_all_unsynced_options[ global_module_id ].indexOf( 'et_pb_content_field' ) : ( '' !== saved_tabs && ( -1 !== saved_tabs.indexOf( 'general' ) || 'all' === saved_tabs ) );

								// no need to perform specific actions if module is fully global, so process it as updated module.
								if ( 'all' === saved_tabs && 'updated' !== selective_sync_method ) {
									et_pb_all_unsynced_options[ global_module_id ] = [];
									selective_sync_method = 'updated';
								}

								if ( _.isObject( shortcode_attributes['named'] ) ) {
									for ( var key in shortcode_attributes['named'] ) {
										if ( 'template_type' !== key && ( 'admin_label' !== key || ( 'admin_label' === key && ! ignore_admin_label ) ) ) {
											// skip unsynced options
											if ( 'updated' === selective_sync_method && -1 !== et_pb_all_unsynced_options[ global_module_id ].indexOf( key ) ) {
												continue;
											}

											var prefixed_key = 'admin_label' !== key ? ( 'et_pb_' + key ) : key;

											if ( '' !== key ) {
												view_settings.model.set( prefixed_key, shortcode_attributes['named'][key], { silent : true } );
											}

											// fill in array of synced legacy optoins to determine which options are not synced
											if ( 'updated' !== selective_sync_method ) {
												et_pb_all_legacy_synced_options[ global_module_id ].push( key );
											}
										}
									}
								}

								if ( sync_content ) {
									var content_storage = 'et_pb_content_new';
									var et_pb_raw_shortcodes = ET_PageBuilder_App.getShortCodeRawContentTags();

									// content storage name is different for raw shortcodes ( such as Code module ). Update the content storage name if needed.
									if ( $.inArray( shortcode_name, et_pb_raw_shortcodes ) > -1 ) {
										content_storage = 'et_pb_raw_content';

										shortcode_content = _.unescape( shortcode_content );
										// replace line-break placeholders with real line-breaks
										shortcode_content = shortcode_content.replace( /<!-- \[et_pb_line_break_holder\] -->/g, '\n' );
									}

									view_settings.model.set( content_storage, shortcode_content, { silent : true } );

									if ( 'updated' !== selective_sync_method ) {
										et_pb_all_legacy_synced_options[ global_module_id ].push( 'et_pb_content_field' );
									}
								}

							if ( 'updated' !== selective_sync_method ) {
								view_settings.model.set( 'legacy_synced_options', et_pb_all_legacy_synced_options[ global_module_id ], { silent : true } );
							}
						} );
					}

					modal_view = new ET_PageBuilder.ModalView( view_settings );
					$( 'body' ).append( modal_view.render().el );

					// Emulate preview clicking if this is triggered via right click
					if ( view_settings.triggered_by_right_click === true && view_settings.do_preview === true ) {
						$('.et-pb-modal-preview-template').trigger( 'click' );
					}

					$( '.et_pb_modal_settings_container' ).addClass( 'et_pb_saved_global_modal et_pb_modal_selective_sync' );
					et_pb_add_selective_sync_buttons( $( '.et_pb_modal_settings_container' ), global_module_id )
				}
			} );
		}

		function et_pb_load_global_row( post_id, module_cid, current_content ) {
			if ( ! $( 'body' ).find( '.et_pb_global_loading_overlay' ).length ) {
				$( 'body' ).append( '<div class="et_pb_global_loading_overlay"></div>' );
			}
			$.ajax( {
				type: "POST",
				url: et_pb_options.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'et_pb_get_global_module',
					et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
					et_global_id : post_id
				},
				success: function( data ){
					var global_content_is_different = false;
					if ( data.error ) {
						// if global template not found, then make module and all child modules not global.
						var this_view = ET_PageBuilder_Layout.getView( module_cid ),
							$child_elements = this_view.$el.find( '[data-cid]' );
						this_view.model.unset( 'et_pb_global_module' );

						if ( $child_elements.length ) {
							$child_elements.each( function() {
								var $this_child = $( this ),
									child_cid = $this_child.data( 'cid' );
								if ( typeof child_cid !== 'undefined' && '' !== child_cid ) {
									var child_view = ET_PageBuilder_Layout.getView( child_cid );
									if ( typeof child_view !== 'undefined' ) {
										child_view.model.unset( 'et_pb_global_parent' );
									}
								}
							});
						}
					} else {
						var processed_content = current_content.replace( / global_parent="\S+"/g, '' );
						var processed_shortcode = data.shortcode.replace( /template_type="\S+"/, 'global_module="' + post_id + '"' );

						// remove all the unwanted spaces and line-breaks to make sure shortcode comparison performed correctly.
						processed_shortcode = processed_shortcode.replace( /]\s?\n\s?\n\s?/g, '] ' ).replace( /\s?\n\s?\n\s?\[/g, ' [' );
						processed_shortcode = processed_shortcode.replace( /]\s+/g, ']' ).replace( /\s+\[/g, '[' );
						processed_content = processed_content.replace( /]\s+/g, ']' ).replace( /\s+\[/g, '[' );

						// remove line-breaks from the shortcode before comparison if editor is in Visual mode
						if ( et_pb_is_editor_in_visual_mode() ) {
							processed_shortcode = processed_shortcode.replace( /\r?\n|\r/g, '' );
						}

						// compare unescaped strings to improve the comparison accuracy
						if ( _.unescape( processed_shortcode ) !== _.unescape( processed_content ) ) {
							global_content_is_different = true;
							// call createLayoutFromContent only if current_shortcode is different than received shortcode
							ET_PageBuilder_App.createLayoutFromContent( data.shortcode, '', '', { ignore_template_tag : 'ignore_template', current_row_cid : module_cid, global_id : post_id, is_reinit : 'reinit' } );
						}
					}

					et_pb_globals_loaded++;

					//make sure all global modules have been processed and reinitialize the layout
					if ( et_pb_globals_requested === et_pb_globals_loaded ) {
						// Reinitialize the layout only if global module content is different than content on page.
						if ( global_content_is_different ) {
							// reinitialize the layout and update global template in DB to make sure all the attributes saved the same way as on the page.
							et_reinitialize_builder_layout( true );
						}

						setTimeout( function(){
							$( 'body' ).find( '.et_pb_global_loading_overlay' ).remove();
						}, 650 );
					}
				}
			} );
		}

		function et_pb_update_global_template( global_module_cid ) {
			var global_module_view           = ET_PageBuilder_Layout.getView( global_module_cid ),
				post_id                      = global_module_view.model.get( 'et_pb_global_module' ),
				layout_type                  = global_module_view.model.get( 'type' );
				layout_type_updated          = 'row_inner' === layout_type ? 'row' : layout_type,
				template_shortcode           = ET_PageBuilder_App.generateCompleteShortcode( global_module_cid, layout_type_updated, 'ignore_global', false, true );
				unsynced_global_options      = ! _.isEmpty( et_pb_all_unsynced_options[ post_id ] ) ? et_pb_all_unsynced_options[ post_id ] : [];

			// do not proceed if global post ID is not defined or empty.
			if ( typeof post_id === 'undefined' || '' === post_id ) {
				return;
			}

			if ( 'row_inner' === layout_type ) {
				template_shortcode = template_shortcode.replace( /et_pb_row_inner/g, 'et_pb_row' );
				template_shortcode = template_shortcode.replace( /et_pb_column_inner/g, 'et_pb_column' );
			}

			$.ajax( {
				type: "POST",
				url: et_pb_options.ajaxurl,
				data:
				{
					action : 'et_pb_update_layout',
					et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
					et_layout_content : template_shortcode,
					et_template_post_id : post_id,
					et_layout_type : layout_type,
					et_unsynced_options : JSON.stringify( unsynced_global_options )
				}
			} );
		}

		function et_pb_get_global_parent_cid( module ) {
			var global_holder_view = module;
			var global_module_cid = '';

			// check the module parents tree to find the closest global parent if exists
			while ( ! _.isUndefined( global_holder_view.model.get( 'parent' ) ) && '' === global_module_cid ) {
				// Refresh global holder for new loop
				global_holder_view = ET_PageBuilder_Layout.getView( global_holder_view.model.get( 'parent' ) );
				global_module_cid = '' === global_module_cid && ! _.isUndefined( global_holder_view.model.get( 'et_pb_global_module' ) ) ? global_holder_view.model.get( 'cid' ) : global_module_cid;
			}

			return global_module_cid;
		}

		function et_pb_open_current_tab() {
			var $container = $( '.et_pb_modal_settings_container' );

			if ( $( '.et_pb_modal_settings_container' ).hasClass( 'et_pb_hide_general_tab' ) ) {
				$container.find( '.et-pb-options-tabs-links li' ).removeClass( 'et-pb-options-tabs-links-active' );
				$container.find( '.et-pb-options-tabs .et-pb-options-tab' ).css( { 'display' : 'none', opacity : 0 } );

				if ( $container.hasClass( 'et_pb_hide_advanced_tab' ) ) {
					$container.find( '.et-pb-options-tabs-links li.et_pb_options_tab_custom_css' ).addClass( 'et-pb-options-tabs-links-active' );
					$container.find( '.et-pb-options-tabs .et-pb-options-tab.et-pb-options-tab-custom_css' ).css( { 'display' : 'block', opacity : 1 } );
				} else {
					$container.find( '.et-pb-options-tabs-links li.et_pb_options_tab_advanced' ).addClass( 'et-pb-options-tabs-links-active' );
					$container.find( '.et-pb-options-tabs .et-pb-options-tab.et-pb-options-tab-advanced' ).css( { 'display' : 'block', opacity : 1 } );
				}
			} else {
				$container.find( '.et-pb-options-tabs .et-pb-options-tab.et-pb-options-tab-general' ).css( { 'display' : 'block', opacity : 1 } );
			}
		}

		/**
		* Check if current user has permission to lock/unlock content
		*/
		function et_pb_user_lock_permissions() {
			var permissions = $.ajax( {
				type: "POST",
				url: et_pb_options.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'et_pb_current_user_can_lock',
					et_admin_load_nonce : et_pb_options.et_admin_load_nonce
				},
				beforeSend : function() {
					ET_PageBuilder_Events.trigger( 'et-pb-loading:started' );
				},
				complete : function() {
					ET_PageBuilder_Events.trigger( 'et-pb-loading:ended' );
				},
			} );

			return permissions;
		}

		/**
		* Check for localStorage support
		*/
		function et_pb_has_storage_support() {
			try {
				return 'localStorage' in window && window.localStorage !== null;
			} catch (e) {
				return false;
			}
		}

		/**
		 * Check whether the Yoast SEO plugin is active
		 */
		function et_pb_is_yoast_seo_active() {
			return ( 'object' === typeof YoastSEO && YoastSEO.hasOwnProperty( 'app' ) )
		}

		function et_pb_is_modal_opened() {
			return 0 < $( '.et_pb_modal_overlay, .et-core-modal-overlay.et-core-active' ).length;
		}

		/**
		 * Prepare an object from hovered module for Shortcuts
		 */
		function et_pb_get_hovered_module_view( hoveredObject ) {
			if ( _.isEmpty( hoveredObject ) || et_pb_is_modal_opened() ) {
				return false;
			}

			var view_settings = {
				model      : hoveredObject.model,
				view       : hoveredObject.$el,
				view_event : event
			};

			var prepared_item = new ET_PageBuilder.RightClickOptionsView( view_settings, true );

			return prepared_item;
		}

		function et_pb_add_selective_sync_buttons( $options_container, global_id ) {
			if ( $options_container.length === 0 ) {
				return;
			}

			var $options_array = $options_container.find( '.et-pb-option' );

			if ( $options_container.find( '.et-pb-option-advanced-module-settings' ).length > 0 ) {
				var disabled_class = et_pb_is_option_unsynced( 'et_pb_content_field', global_id ) ? ' et_pb_global_unsynced' : '';

				$options_container.find( '.et-pb-option-advanced-module-settings' ).append( '<span class="et_pb_global_sync_switcher' + disabled_class + '" data-option_name="et_pb_content_field" data-additional_options="none"></span>' );
			}

			if ( $options_array.length === 0 ) {
				return;
			}

			_.each( $options_array, function( $single_option ) {
				var option_name = 'content_new' === $( $single_option ).data( 'option_name' ) ? 'et_pb_content_field' : $( $single_option ).data( 'option_name' );
				var disabled_class = et_pb_is_option_unsynced( option_name, global_id ) ? ' et_pb_global_unsynced' : '';
				var additional_options = $( $single_option ).find( '.et_pb_mobile_settings_tabs' ).length !== 0 ? 'mobile' : 'none';

				$( $single_option ).append( '<span class="et_pb_global_sync_switcher' + disabled_class + '" data-option_name="' + option_name + '" data-additional_options="' + additional_options + '"></span>' );
			} );
		}

		function et_pb_is_option_unsynced( option_name, global_id ) {
			var sync_method = ! _.isUndefined( et_pb_all_unsynced_options[ global_id ] ) ? 'updated' : 'legacy';

			if ( 'legacy' === sync_method ) {
				return ( -1 === et_pb_all_legacy_synced_options[ global_id ].indexOf( option_name ) );
			} else {
				return ( -1 !== et_pb_all_unsynced_options[ global_id ].indexOf( option_name ) );
			}
		}

		/**
		 * Check if path exists in object.
		 *
		 * @see https://stackoverflow.com/a/42042678/2639936
		 *
		 * @param obj
		 * @param path
		 * @return {boolean}
		 */
		function has( obj, path ) {
			if( ! path ) {
				return true;
			}

			var path_parts = path.split( '.' );
			var first_part = _.first( path_parts );

			return _.has( obj, first_part ) && has( obj[first_part], _.rest( path_parts ).join( '.' ) );
		}

		/**
		 * Removes extra <p> and <br> tags from shortcode similar to et_pb_fix_shortcodes from /includes/builder/functions.php
		 * @return {string}
		 */
		function et_pb_fix_shortcodes( shortcode ) {
			shortcode = shortcode.replace( /<p>\[/g, '[' );
			shortcode = shortcode.replace( /\]<\/p>/g, ']' );
			shortcode = shortcode.replace( /\]<br \/>/g, ']' );
			shortcode = shortcode.replace( /<br \/>\n\[/g, '[' );

			return shortcode;
		}

		/**
		* Clipboard mechanism. Clipboard is only capable of handling one copied content at the onetime
		* @todo add fallback support
		*/
		ET_PB_Clipboard = {
			key : 'et_pb_clipboard_',
			set : function( type, content ) {
				if ( et_pb_has_storage_support() ) {
					// Save the type of copied content
					localStorage.setItem( this.key + 'type', type );

					// Save the copied content
					localStorage.setItem( this.key + 'content', content );
				} else {
					alert( et_pb_options.localstorage_unavailability_alert );
				}
			},
			get : function( type ) {
				if ( et_pb_has_storage_support() ) {
					// Get saved type and content
					var saved_type =  localStorage.getItem( this.key + 'type' ),
						saved_content = localStorage.getItem( this.key + 'content' );

					// Check for the compatibility of saved data and paste destination
					// Return value if the supplied type equal with saved value, or if the getter doesn't care about the content's type
					if ( typeof type === 'undefined' || type === saved_type ) {
						return saved_content;
					} else {
						return false;
					}
				} else {
					alert( et_pb_options.localstorage_unavailability_alert );
				}
			}
		};

		var et_pb_reinit_layout_throttled = _.debounce( et_reinitialize_builder_layout, 2000 );

		/**
		* Builder hotkeys
		*/
		$(window).keydown( function( event ){
			if ( ! et_pb_is_builder_used() ) {
				return;
			}

			function et_pb_close_opened_modal() {
				var $close_button = $( '.et-pb-modal-close' );
				var $core_close_button = $('.et-core-modal-close');

				if ( $close_button.length ) {
					// it's possible that there are 2 Modals appear on top of each other, close the one which is on top
					if ( typeof $close_button[1] !== 'undefined' ) {
						$close_button[1].click();
					} else {
						$close_button.click();
					}
				}

				if ($core_close_button.length) {
					$core_close_button.click();
				}

				$( 'body' ).removeClass( 'et-core-nbfc' );
			}

			// Hotkeys that should work regardless current focus state

			// Handle Esc and Enter only if Modal is opened
			if ( et_pb_is_modal_opened() || $( '.et_pb_prompt_modal' ).is( ':visible' ) ) {
				var $save_button = $( '.et-pb-modal-save' ),
					$proceed_button = $( '.et_pb_prompt_proceed' ),
					$builder_buttons = $( '#et_pb_main_container a, #et_pb_toggle_builder' );

				switch( event.which ) {
					// Enter button handling
					case 13 :
						// do nothing if focus is in the textarea or in the map address field so enter will work as expected
						if ( $( '.et-pb-option-container textarea, #et_pb_address, #et_pb_pin_address, .et-pb-color-picker-hex-has-preview' ).is( ':focus' ) ) {

							// Close currently focused colorpicker
							$( '.et-pb-color-picker-hex-has-preview:focus' ).wpColorPicker( 'close' );
							return;
						}
						//remove focus from the builder buttons to avoid unexpected behavior
						$builder_buttons.blur();

						if ( $save_button.length || $proceed_button.length ) {
							// it's possible that proceed button displayed above the save, we need to click only proceed button in that case
							if ( $proceed_button.length ) {
								$proceed_button.click();
							} else {
								// it's possible that there are 2 Modals appear on top of each other, save the one which is on top
								if ( typeof $save_button[1] !== 'undefined' ) {
									$save_button[1].click();
								} else {
									$save_button.click();
								}
							}
						}
						break;
					// Escape button handling
					case 27 :
						// close the modal on Esc
						et_pb_close_opened_modal();
						break;
				}
			}

			if ((event.keyCode === 83 && event.metaKey && event.shiftKey && !event.altKey) || (event.keyCode === 83 && event.ctrlKey && event.shiftKey && !event.altKey)) {
				// Save as draft
				event.preventDefault();

				et_pb_close_opened_modal();

				// Triggers save by draft by triggering DOM to initiate necessary animation
				$('#save-post').trigger('click');

				return;
			} else if ( (event.keyCode === 83 && event.metaKey && !event.altKey) || (event.keyCode === 83 && event.ctrlKey && !event.altKey) ) {
				// Save / Publish
				event.preventDefault();

				et_pb_close_opened_modal();

				// Triggers publish by triggering DOM to initiate necessary animation
				jQuery('#publish').trigger('click');

				return;
			}


			// do not override default hotkeys inside input fields
			if ( typeof event.target !== 'undefined' && $( event.target ).is( 'input, textarea' ) ) {
				return;
			}

			if ( event.keyCode === 90 && event.metaKey && event.shiftKey && ! event.altKey || event.keyCode === 90 && event.ctrlKey && event.shiftKey && ! event.altKey ) {
				// Redo
				event.preventDefault();

				ET_PageBuilder_App.redo( event );

				return false;
			} else if ( event.keyCode === 90 && event.metaKey && ! event.altKey || event.keyCode === 90 && event.ctrlKey && ! event.altKey ) {
				// Undo
				event.preventDefault();

				ET_PageBuilder_App.undo( event );

				return false;
			} else if ( event.keyCode === 79 ) {
				// do not proceed if any modal is opened
				if ( et_pb_is_modal_opened() ) {
					return;
				}

				// Open Page Settings Modal: `o`
				event.preventDefault();

				if (!$('.et_pb_builder_settings').length) {
					$('#et_pb_layout .et-pb-app-settings-button').trigger('click');
				}
			} else if (event.keyCode === 80 ) {

				event.preventDefault();

				// Open preview on super + `p`
				if ( event.metaKey || event.ctrlKey ) {
					if ( $( '.et-pb-modal-preview-template' ).length ) {
						$( '.et-pb-modal-preview-template' ).trigger( 'click' );
					}

					return false;
				}

				// do not proceed if any modal is opened
				if ( et_pb_is_modal_opened() ) {
					return;
				}

				// Open Portability Tooltip: `p`
				if (!$('div[data-et-core-portability]').hasClass('et-core-active')) {
					$('#et_pb_layout .et-pb-app-portability-button').trigger('click');
				}
			} else if (event.keyCode === 72) {
				// do not proceed if any modal is opened
				if ( et_pb_is_modal_opened() ) {
					return;
				}

				// Open History Modal: `h`
				event.preventDefault();

				$('#et_pb_layout .et-pb-layout-buttons-history').trigger('click');
			} else if (event.keyCode === 9  && event.shiftKey) {
				// Switch modal tabs on `Tab` key
				if ( $( '.et-pb-options-tabs-links' ).length || $( '.et-pb-preview-screensize-switcher' ).length ) {
					var isPreviewActive = $( '.et-pb-modal-preview-template' ).length && $( '.et-pb-modal-preview-template' ).hasClass( 'active' ) ? true : false;
					var $tabsContainer = ! isPreviewActive ? $( '.et-pb-options-tabs-links' ) : $( '.et-pb-preview-screensize-switcher' );
					var $tabLinks = $tabsContainer.find( 'li' );
					var tabsCount = $tabLinks.length;
					var nextTab = $tabLinks[0];
					var counter = 0;

					$tabLinks.each( function( index ) {
						var activeClassHolder = isPreviewActive ? $( this ).find( 'a' ) : $( this );
						var activeClass = isPreviewActive ? 'active' : 'et-pb-options-tabs-links-active';
						// if we're not on the last tab
						if ( activeClassHolder.hasClass( activeClass ) && index !== tabsCount - 1 ) {
							nextTab = $tabLinks[ index + 1 ];
						}
					});

					if ( $( nextTab ).length ) {
						$( nextTab ).find( 'a' ).trigger( 'click' );
					}
				}
			} else if (event.keyCode === 67 && ( event.metaKey || event.ctrlKey )) {
				// Copy hovered module on super + `c`
				var prepared_item = et_pb_get_hovered_module_view( et_pb_hovered_item_buffer );

				if ( prepared_item ) {
					// do not copy empty columns
					if ( et_pb_hovered_item_buffer.$el.hasClass( 'et-pb-column' ) ) {
						return;
					}

					prepared_item.copy( event );
				}
			} else if (event.keyCode === 86 && ( event.metaKey || event.ctrlKey )) {
				// Paste after hovered module on super + `v`
				var prepared_item = et_pb_get_hovered_module_view( et_pb_hovered_item_buffer );

				if ( prepared_item ) {
					var $hoveredElement = et_pb_hovered_item_buffer.$el;

					if ( $hoveredElement.hasClass( 'et-pb-column' ) || ( $hoveredElement.hasClass( 'et_pb_section_fullwidth' ) && ! ET_PB_Clipboard.get( 'et_pb_clipboard_section' ) && ! $hoveredElement.find( '.et_pb_module_block' ).length ) ) {
						prepared_item.pasteColumn( event );
					} else {
						prepared_item.pasteAfter( event );
					}

				}
			} else if (event.keyCode === 88 && ( event.metaKey || event.ctrlKey )) {
				// Cut hovered module on super + `x`

				var prepared_item = et_pb_get_hovered_module_view( et_pb_hovered_item_buffer );

				if ( prepared_item ) {
					// do not copy empty columns
					if ( et_pb_hovered_item_buffer.$el.hasClass( 'et-pb-column' ) ) {
						return;
					}

					prepared_item.copy( event );

					var $remove_button = et_pb_hovered_item_buffer.$el.hasClass( 'et_pb_module_block' ) ? et_pb_hovered_item_buffer.$el.find( '.et-pb-remove' ) : et_pb_hovered_item_buffer.$el.find( '> .et-pb-controls .et-pb-remove' );

					// remove element after it was copied
					if ( $remove_button.length ) {
						$remove_button.trigger( 'click' );
						et_pb_hovered_item_buffer = {};
						et_reinitialize_builder_layout();
					}
				}
			} else if (event.keyCode === 68 ) {
				//Disable module `d`
				var prepared_item = et_pb_get_hovered_module_view( et_pb_hovered_item_buffer );

				if ( prepared_item ) {
					// do not proceed if empty column hovered
					if ( et_pb_hovered_item_buffer.$el.hasClass( 'et-pb-column' ) ) {
						return;
					}

					var history_verb = 'disabled';

					if ( typeof prepared_item.model.attributes.et_pb_disabled === 'undefined' || 'on' !== prepared_item.model.attributes.et_pb_disabled ) {
						prepared_item.model.attributes.et_pb_disabled = 'on';
						et_pb_hovered_item_buffer.$el.addClass( 'et_pb_disabled' );
					} else {
						prepared_item.model.attributes.et_pb_disabled = 'off';
						et_pb_hovered_item_buffer.$el.removeClass( 'et_pb_disabled' );
						history_verb = 'enabled'
					}

					// Update global module
					prepared_item.updateGlobalModule();

					// Enable history saving and set meta for history
					ET_PageBuilder_App.allowHistorySaving( history_verb, prepared_item.history_noun );

					ET_PageBuilder_App.saveAsShortcode();
				}
			} else if (event.keyCode === 76 ) {
				//Lock module `l`

				var prepared_item = et_pb_get_hovered_module_view( et_pb_hovered_item_buffer );

				if ( prepared_item ) {
					// do not proceed if empty column hovered
					if ( et_pb_hovered_item_buffer.$el.hasClass( 'et-pb-column' ) ) {
						return;
					}

					if ( typeof prepared_item.model.attributes.et_pb_locked === 'undefined' || 'on' !== prepared_item.model.attributes.et_pb_locked ) {
						prepared_item.lock( event );
					} else {
						prepared_item.unlockItem( event );
					}

					et_reinitialize_builder_layout();
				}
			} else if (event.keyCode === 83) {
				event.preventDefault();

				if ( ! _.isEmpty( et_pb_hovered_item_buffer ) && ! $( '.et_pb_modal_overlay' ).length ) {
					// save the `s` key pressed flag
					et_pb_key_pressed.s = true;
				}
			} else if ( ( event.keyCode === 49 || event.keyCode === 50 || event.keyCode === 51 ) && et_pb_key_pressed.s ) {
				if ( $( '.et_pb_modal_overlay' ).length ) {
					return;
				}

				var $hoveredElement = et_pb_hovered_item_buffer.$el;
				var $hoveredSection = $hoveredElement.closest( '.et_pb_section' );

				if ( $hoveredSection.length ) {
					switch( event.keyCode ) {
						case 49:
							// add regular section
							$hoveredSection.find( '.et-pb-section-add-main' ).trigger( 'click' );
						  break;
						case 50:
							//add specialty section
							$hoveredSection.find( '.et-pb-section-add-specialty' ).trigger( 'click' );
						  break;
						case 51:
							//add fullwidth section
							$hoveredSection.find( '.et-pb-section-add-fullwidth' ).trigger( 'click' );
						  break;
					}
				}
			} else if (event.keyCode === 82) {
				if ( ! _.isEmpty( et_pb_hovered_item_buffer ) && ! $( '.et_pb_modal_overlay' ).length ) {
					// save the `r` key pressed flag
					et_pb_key_pressed.r = true;
				}
			} else if (event.keyCode === 67 && ! event.metaKey && ! event.ctrlKey ) {
				if ( ! _.isEmpty( et_pb_hovered_item_buffer ) && ! $( '.et_pb_modal_overlay' ).length ) {
					// save the `c` key pressed flag
					et_pb_key_pressed.c = true;
				}
			}  else if ( ( event.keyCode === 49 || event.keyCode === 50 || event.keyCode === 51 || event.keyCode === 52 || event.keyCode === 53 || event.keyCode === 54 || event.keyCode === 55 || event.keyCode === 56 || event.keyCode === 57 || event.keyCode === 48 || event.keyCode === 189 ) && ( et_pb_key_pressed.r || et_pb_key_pressed.c ) ) {
				// Add Row / Change Row Structure shortcuts

				var $hoveredElement = et_pb_hovered_item_buffer.$el;
				var $hoveredRow = $hoveredElement.closest( '.et_pb_row' );

				if ( $hoveredRow.length ) {
					var selectedLayout = '4_4';

					switch( event.keyCode ) {
						case 49:
							selectedLayout = '4_4';
						  break;
						case 50:
							selectedLayout = '1_2,1_2';
						  break;
						case 51:
							selectedLayout = '1_3,1_3,1_3';
						  break;
						case 52:
							selectedLayout = '1_4,1_4,1_4,1_4';
						  break;
						case 53:
							selectedLayout = '2_3,1_3';
						  break;
						case 54:
							selectedLayout = '1_3,2_3';
						  break;
						case 55:
							selectedLayout = '1_4,3_4';
						  break;
						case 56:
							selectedLayout = '3_4,1_4';
						  break;
						case 57:
							selectedLayout = '1_2,1_4,1_4';
						  break;
						case 48:
							selectedLayout = '1_4,1_4,1_2';
						  break;
						case 189:
							selectedLayout = '1_4,1_2,1_4';
						  break;
					}

					var row_view = ET_PageBuilder_Layout.getView( $hoveredRow.find( '.et-pb-row-content' ).data( 'cid' ) );

					if ( typeof row_view !== 'undefined' ) {
						var is_structure_change = false;
						var skip_column_history = false;

						var row_parent = ET_PageBuilder_Layout.getView( row_view.model.attributes.parent );

						if ( typeof row_parent !== 'undefined' && 'column' === row_parent.model.attributes.type ) {
							var allowed_columns = 3 === row_parent.model.attributes.specialty_columns ? [ 49, 50, 51 ] : [ 49, 50 ];

							// do not insert unsupported column type into the specialty section
							if ( -1 === $.inArray( event.keyCode, allowed_columns ) ) {
								return;
							}
						}

						var processed_row_view = {};

						if ( et_pb_key_pressed.r ) {

							if ( 'on' === row_view.model.get( 'et_pb_parent_locked' ) ) {
								return;
							}

							// Split Testing-related action
							if ( ET_PageBuilder_AB_Testing.is_active() ) {

								// Check for user permission and module status
								if ( ! ET_PageBuilder_AB_Testing.is_user_has_permission( row_view.model.get( 'cid' ), 'add_row' ) ) {
									ET_PageBuilder_AB_Testing.alert( 'has_no_permission' );
									return;
								}
							}

							// Enable history saving and set meta for history
							ET_PageBuilder_App.allowHistorySaving( 'added', 'row' );

							skip_column_history = true;

							// creating new Row
							var module_id = ET_PageBuilder_Layout.generateNewId(),
								global_parent = typeof row_view.model.get( 'et_pb_global_module' ) !== 'undefined' && '' !== row_view.model.get( 'et_pb_global_module' ) ? row_view.model.get( 'et_pb_global_module' ) : '',
								global_parent_cid = '' !== global_parent ? row_view.model.get( 'cid' ) : '';

							row_parent.collection.add( [ {
								type : 'row',
								module_type : 'row',
								cid : module_id,
								parent : row_parent.model.get( 'cid' ),
								view : row_view,
								appendAfter : row_view.$el,
								et_pb_global_parent : global_parent,
								global_parent_cid : global_parent_cid,
								admin_label : et_pb_options.noun['row']
							} ] );
							processed_row_view = ET_PageBuilder_Layout.getView( module_id );
						} else {
							// structure edit is not allowed for global Rows
							if ( ( typeof row_view.model.attributes.et_pb_global_module !== 'undefined' && '' !== row_view.model.attributes.et_pb_global_module ) || ( 'row' === et_pb_options.layout_type && 'global' === et_pb_options.is_global_template ) ) {
								return;
							}

							// changing structure of exisitng Row
							processed_row_view = row_view;
							is_structure_change = true;
						}

						var column_options = {
							'layout' : selectedLayout,
							'is_structure_change' : is_structure_change,
							'layout_specialty' : '',
						};

						// reset the columns layout
						ET_PageBuilder_Layout.changeColumnStructure( processed_row_view, column_options, true, skip_column_history );
					}
				}
			} else if ( ( event.keyCode === 49 || event.keyCode === 50 || event.keyCode === 51 || event.keyCode === 52 || event.keyCode === 53 || event.keyCode === 54 || event.keyCode === 55 || event.keyCode === 56 || event.keyCode === 57 || event.keyCode === 48 || event.keyCode === 189 ) ) {
				if ( ! $( 'ul.et-pb-column-layouts' ).length ) {
					return;
				}
				var layoutsList = $( 'ul.et-pb-column-layouts li' );
				var selectedLayout = 0;

				switch( event.keyCode ) {
					case 49:
						selectedLayout = 0;
						break;
					case 50:
						selectedLayout = 1;
						break;
					case 51:
						selectedLayout = 2;
						break;
					case 52:
						selectedLayout = 3;
						break;
					case 53:
						selectedLayout = 4;
						break;
					case 54:
						selectedLayout = 5;
						break;
					case 55:
						selectedLayout = 6;
						break;
					case 56:
						selectedLayout = 7;
						break;
					case 57:
						selectedLayout = 8;
						break;
					case 48:
						selectedLayout = 9;
						break;
					case 189:
						selectedLayout = 10;
						break;
				}

				if ( layoutsList.length && layoutsList[ selectedLayout ] ) {
					$( layoutsList[ selectedLayout ] ).trigger( 'click' );
				}
			} else if (event.key === '?' || event.keyCode === 191) {
				var isHelpModal = $('.et_pb_modal_settings_container').attr('data-open_view') === 'help';
				// Help : `?`
				if ( $('.et-pb-modal-close').length ) {
					$('.et-pb-modal-close').click();
				}

				if (isHelpModal) {
					return;
				}

				et_pb_close_opened_modal();

				view = new ET_PageBuilder.ModalView( {
					attributes : {
						'data-open_view' : 'help'
					},
					view : this
				} );

				$('body').append( view.render().el );
			}
		});

		$(window).keyup( function( event ) {
			if (event.keyCode === 83) {
				// reset the `s` key pressed flag
				et_pb_key_pressed.s = false;
			} else if ( event.keyCode === 82 ) {
				// reset the `r` key pressed flag
				et_pb_key_pressed.r = false;
			} else if ( event.keyCode === 67 && et_pb_key_pressed.c ) {
				// reset the `c` key pressed flag
				et_pb_key_pressed.c = false;
				et_pb_reinit_layout_throttled();
			}
		});

		$( 'body' ).on( 'mouseover', '.et-pb-right-click-trigger-overlay, .et-pb-controls, .et_pb_module_block, .et-pb-insert-module, .et-pb-row-add', function( event ) {
			var $hoveredItem = $( event.target );
			var $hoveredElement = $hoveredItem.closest( '.et_pb_module_block' );

			if ( ! $hoveredElement.length ) {
				// if empty Column or Row hovered
				if ( $hoveredItem.closest( '.et-pb-insert-module' ).length || $hoveredItem.closest( '.et-pb-row-add' ).length ) {

					$hoveredElement = $hoveredItem.closest( 'div' );

					if ( $hoveredItem.closest( '.et-pb-row-add' ).length ) {
						$hoveredElement = $hoveredElement.find( '.et-pb-row-content' );
					}
				} else {
					var $hoveredRightClickArea = $hoveredItem.closest( '.et-pb-right-click-trigger-overlay' ).length ? $hoveredItem.closest( '.et-pb-right-click-trigger-overlay' ) : $hoveredItem.closest( '.et-pb-controls' );

					// do not proceed if no Divi Module hovered and reset hovered element
					if ( ! $hoveredRightClickArea.length ) {
						et_pb_hovered_item_buffer = {};
						return;
					}

					var $hoveredRow = $hoveredRightClickArea.closest( '.et_pb_row' );

					if ( $hoveredRow.length ) {
						$hoveredElement = $hoveredRow.find( '.et-pb-row-content' );
					} else {
						var $hoveredSection = $hoveredRightClickArea.closest( '.et_pb_section' );
						$hoveredElement = $hoveredSection.find( '.et-pb-section-content' );
					}
				}
			}

			if ( $hoveredElement.length ) {
				var hoveredElementObject = ET_PageBuilder_Layout.getView( $hoveredElement.data( 'cid' ) );
				et_pb_hovered_item_buffer = hoveredElementObject;
			}
		} );

		// open module settings on double click
		$( 'body' ).on( 'dblclick', '.et-pb-right-click-trigger-overlay, .et-pb-controls, .et_pb_module_block', function( event) {
			var $clickedItem = $( event.target );

			// do not proceed if clicked on buttons
			if ( $clickedItem.closest( 'a' ).length ) {
				return;
			}

			var $clickedModule = $clickedItem.closest( '.et_pb_module_block' );

			if ( $clickedModule.length ) {
				// Open module settings
				$clickedModule.find( '.et-pb-settings' ).trigger( 'click' );
				return;
			}

			if ( $clickedItem.closest( '.et-pb-controls' ).length ) {
				// Open settings of Row or Section if clicked inside the controls panel
				$clickedItem.closest( '.et-pb-controls' ).find( '.et-pb-settings' ).trigger( 'click' );
			}

			var $clickedRightClickArea = $clickedItem.closest( '.et-pb-right-click-trigger-overlay' );

			if ( ! $clickedRightClickArea.length ) {
				return;
			}

			var $clickedRow = $clickedRightClickArea.closest( '.et_pb_row' );

			if ( $clickedRow.length ) {
				// Open Row Settings
				$clickedRow.find( '> .et-pb-controls .et-pb-settings' ).trigger( 'click' );
			} else {
				var $clickedSection = $clickedRightClickArea.closest( '.et_pb_section' );
				// Open Section Settings
				$clickedSection.find( '> .et-pb-controls .et-pb-settings' ).trigger( 'click' );
			}
		} );


		/**
		 * Add app settings button and link it to actual settings button
		 * Hide settings button on Divi Library
		 */
		if ( et_pb_options.is_divi_library === "0" ) {
			var $app_settings_button = $( '#et-builder-app-settings-button-template' ).html();

			$( '#et_pb_layout' ).prepend( $app_settings_button );

			$( '#et_pb_layout' ).on( 'click', '.et-pb-app-view-ab-stats-button', function(e) {
				e.preventDefault();

				if ( ET_PageBuilder_AB_Testing.is_selecting_subject() ) {
					ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_subject_first' );
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting_goal() ) {
					ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_goal_first' );
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting_winner() ) {
					ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_winner_first' );
					return;
				}

				$( '#et_pb_layout_controls .et-pb-layout-buttons-view-ab-stats' ).trigger( 'click' );
			} );

			$( '#et_pb_layout' ).on( 'click', '.et-pb-app-portability-button.et-core-disabled', function(e) {
				e.preventDefault();

				if ( ET_PageBuilder_AB_Testing.is_selecting_subject() ) {
					ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_subject_first' );
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting_goal() ) {
					ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_goal_first' );
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting_winner() ) {
					ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_winner_first' );
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_active() ) {
					ET_PageBuilder_AB_Testing.alert( 'cannot_import_export_layout_has_ab_testing' );
					return;
				}
			} );

			$( '#et_pb_layout' ).on( 'click', '.et-pb-app-settings-button', function(e) {
				e.preventDefault();

				if ( ET_PageBuilder_AB_Testing.is_selecting_subject() ) {
					ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_subject_first' );
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting_goal() ) {
					ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_goal_first' );
					return;
				}

				if ( ET_PageBuilder_AB_Testing.is_selecting_winner() ) {
					ET_PageBuilder_AB_Testing.alert( 'select_ab_testing_winner_first' );
					return;
				}

				$( '#et_pb_layout_controls .et-pb-layout-buttons-settings' ).trigger( 'click' );
			} );
		}

		// set the correct content for Yoast SEO plugin if it's activated
		if ( et_pb_is_yoast_seo_active() ) {
			var ET_PB_Yoast_Content = function() {
				YoastSEO.app.registerPlugin( 'ET_PB_Yoast_Content', { status: 'ready' } );

				/**
				 * @param modification    {string}    The name of the filter
				 * @param callable        {function}  The callable
				 * @param pluginName      {string}    The plugin that is registering the modification.
				 * @param priority        {number}    (optional) Used to specify the order in which the callables
				 *                                    associated with a particular filter are called. Lower numbers
				 *                                    correspond with earlier execution.
				 */
				YoastSEO.app.registerModification( 'content', this.et_pb_update_content, 'ET_PB_Yoast_Content', 5 );
			}

			/**
			 * Return the content processed by do_shortcode()
			 */
			ET_PB_Yoast_Content.prototype.et_pb_update_content = function( data ) {
				var final_content = et_pb_processed_yoast_content || et_pb_options.yoast_content;

				return final_content;
			};

			new ET_PB_Yoast_Content();
		}

		$( window ).resize( function() {
			var $et_pb_prompt_modal = $('.et_pb_prompt_modal.et_pb_auto_centerize_modal');

			if ( $et_pb_prompt_modal.length ) {
				$et_pb_prompt_modal.removeAttr( 'style' );
				window.et_pb_align_vertical_modal( $et_pb_prompt_modal, '.et_pb_prompt_buttons' );
			}
		} );
	} );

} )(jQuery);

( function($) {

	window.et_builder = window.et_builder || {};

	/* Override the Yoast function to fix the typing lag caused by Yoast seo plugin
	 * By default Yoast parses shortcodes from all the content on every keypress inside tinyMCE
	 * and it causes the lag during the typing if content of page is huge
	 * Basically just remove the following part from original function:
	 *		e.editor.on('keydown', function() {
	 *			that.loadShortcodes.bind( that, that.declareReloaded.bind( that ) )();
	 *		});
	 */
	if ( typeof window.YoastShortcodePlugin !== 'undefined' ) {
		window.YoastShortcodePlugin.prototype.bindElementEvents = function() {
			var contentElement = document.getElementById( 'content' ) || false;
			var that = this;

			if (contentElement) {
				contentElement.addEventListener( 'keydown', this.loadShortcodes.bind( this, this.declareReloaded.bind( this ) ) );
				contentElement.addEventListener( 'change', this.loadShortcodes.bind( this, this.declareReloaded.bind( this ) ) );
			}

			if( typeof tinyMCE !== 'undefined' && typeof tinyMCE.on === 'function' ) {
				tinyMCE.on( 'addEditor', function( e ) {
					e.editor.on( 'change', function() {
						that.loadShortcodes.bind( that, that.declareReloaded.bind( that ) )();
					});
				});
			}
		}
	}

	$( document ).ready( function() {
		var et_builder = {},
			et_builder_template_options = {
				tabs: {},
				padding: {},
				yes_no_button: {},
				font_buttons: {}
			},

			$et_toggle_builder_button = $('#et_pb_toggle_builder'),
			$et_pb_fb_cta = $( '#et_pb_fb_cta' );

			if ( $et_toggle_builder_button.hasClass( 'et_pb_builder_is_used' ) ) {
				$et_pb_fb_cta.show();
			}

		window.et_builder_template_options = et_builder_template_options;

		// hook for necessary adv form field logic for tabbed posts module
		function adv_setting_form_category_select_update_hidden( that ) {
			$select_field = that.$el.find('#et_pb_category_id');
			$hidden_name_field = that.$el.find('#et_pb_category_name');

			if ( $select_field.length && $hidden_name_field.length ) {
				category_name = $select_field.find('option:selected').text().trim();
				$hidden_name_field.val( category_name );

				$select_field.on('change', function() {
					category_name = $(this).find('option:selected').text().trim();
					$hidden_name_field.val( category_name );
				});
			}
		}
		ET_PageBuilder.Events.on('et-advanced-module-settings:render', adv_setting_form_category_select_update_hidden );

		et_builder = {
			fonts_template: function() {
				var template = $('#et-builder-google-fonts-options-items').html();

				return template;
			},
			font_icon_list_template: function(){
				var template = $('#et-builder-font-icon-list-items').html();

				return template;
			},
			font_down_icon_list_template: function(){
				var template = $('#et-builder-font-down-icon-list-items').html();

				return template;
			},
			preview_tabs_output: function(){
				var template = $('#et-builder-preview-icons-template').html();

				return template;
			},
			options_tabs_output: function( options ){
				var template = _.template( $('#et-builder-options-tabs-links-template').html() ),
					options_filtered = {},
					options_filtered_index = 1,
					template_processed;

				window.et_builder_template_options['tabs']['options'] = $.extend( {}, options );

				template_processed = template( window.et_builder_template_options.tabs );

				return template_processed;
			},
			mobile_tabs_output: function(){
				var template = $('#et-builder-mobile-options-tabs-template').html();

				return template;
			},
			options_padding_output: function( options ){
				var template = _.template( $('#et-builder-padding-inputs-template').html() ),
					template_processed;

				window.et_builder_template_options['padding']['options'] = $.extend( {}, options );

				template_processed = template( window.et_builder_template_options.padding );

				return template_processed;
			},
			options_yes_no_button_output: function( options ){
				var template = _.template( $('#et-builder-yes-no-button-template').html() ),
					template_processed;

				window.et_builder_template_options['yes_no_button']['options'] = $.extend( {}, options );

				template_processed = template( window.et_builder_template_options.yes_no_button );

				return template_processed;
			},
			options_font_buttons_output: function( options ){
				var template = _.template( $('#et-builder-font-buttons-option-template').html() ),
					template_processed;

				window.et_builder_template_options['font_buttons']['options'] = $.extend( {}, options );

				template_processed = template( window.et_builder_template_options.font_buttons );

				return template_processed;
			}
		};

		$.extend( window.et_builder, et_builder );

		// Adjust the height of tinymce iframe when fullscreen mode enabled from the Divi builder
		function et_pb_adjust_fullscreen_mode() {
			var $modal_container = $( '.et_pb_modal_settings_container' );

			// if fullscreen mode enabled then calculate and apply correct height
			if ( $modal_container.find( 'div.mce-fullscreen' ).length ) {
				setTimeout( function() {
					var modal_height = $modal_container.innerHeight(),
						toolbar_height = $modal_container.find( '.mce-toolbar-grp' ).innerHeight();

					$modal_container.find( 'iframe' ).height( modal_height - toolbar_height );
				}, 100 );
			}
		}

		// recalculate sizes of tinymce iframe when Fullscreen button clicked
		$( 'body' ).on( 'click', '.et_pb_module_settings .mce-i-fullscreen', function() {
			et_pb_adjust_fullscreen_mode();
		});

		// recalculate sizes of tinymce iframe when window resized
		$( window ).resize( function() {
			et_pb_adjust_fullscreen_mode();
		});

		// Fixing fullscreen editor inside builder ModalView height in Firefox. Firefox is too fast in calculating modal weight
		// Its height calculation ends up incorrect. Performing delayed resize trigger fixes the issue
		$('body.wp-admin').on( 'click', '.et-pb-modal-container .mce-widget.mce-btn[aria-label="Fullscreen"] button', function() {
			setTimeout( function() {
				$(window).trigger( 'resize' );
			}, 50 );
		} );
	});

} )(jQuery);
