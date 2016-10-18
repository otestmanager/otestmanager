Ext.enableFx = false;

/**
*	Ext Apply
*		START
*/

Ext.apply(Ext.form.VTypes, {
	daterange: function(val, field){
		var date = field.parseDate(val);
		if (!date) { return; }
		if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
			var start = Ext.getCmp(field.startDateField);
			start.setMaxValue(date);
			/*start.validate();*/
			this.dateRangeMax = date;
		} else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
			var end = Ext.getCmp(field.endDateField);
			if (end) {
				end.setMinValue(date);
				/*end.validate();*/
			}
			this.dateRangeMin = date;
		}
		return true;
	}
});
/**
*	Ext Apply
*		END
*/


/**
*	Ext Define
*		START
*/
Ext.define('Ext.ux.chart.LegendItem.Unclickable', {
	override: 'Ext.chart.LegendItem',
	onMouseDown: function() {
		if (this.legend.clickable !== false) {
			this.callParent(arguments);
		}
	},
	onMouseOver: function() {
		if (this.legend.clickable !== false) {
			this.callParent(arguments);
		}
	},
	onMouseOut: function() {
		if (this.legend.clickable !== false) {
			this.callParent(arguments);
		}
	}
});


/**
 * The Preview Plugin enables toggle of a configurable preview of all visible records.
 *
 * Note: This plugin does NOT assert itself against an existing RowBody feature and may conflict with
 * another instance of the same plugin.
 */
Ext.define('Ext.ux.PreviewPlugin', {
	extend: 'Ext.plugin.Abstract',
	alias: 'plugin.preview',
	requires: ['Ext.grid.feature.RowBody'],

	// private, css class to use to hide the body
	hideBodyCls: 'x-grid-row-body-hidden',

	/**
	 * @cfg {String} bodyField
	 * Field to display in the preview. Must be a field within the Model definition
	 * that the store is using.
	 */
	bodyField: '',

	/**
	 * @cfg {Boolean} previewExpanded
	 */
	previewExpanded: true,

	/**
	 * Plugin may be safely declared on either a panel.Grid or a Grid View/viewConfig
	 * @param {Ext.grid.Panel/Ext.view.View} target
	 */
	setCmp: function(target) {
		this.callParent(arguments);

		// Resolve grid from view as necessary
		var me = this,
			grid        = me.cmp = target.isXType('gridview') ? target.grid : target,
			bodyField   = me.bodyField,
			hideBodyCls = me.hideBodyCls,
			feature     = Ext.create('Ext.grid.feature.RowBody', {
				grid : grid,
				getAdditionalData: function(data, idx, model, rowValues) {

					var getAdditionalData = Ext.grid.feature.RowBody.prototype.getAdditionalData,
						additionalData = {
							rowBody: data[bodyField],
							rowBodyCls: grid.getView().previewExpanded ? '' : hideBodyCls
						};

					if (Ext.isFunction(getAdditionalData)) {
						// "this" is the RowBody object hjere. Do not change to "me"
						Ext.apply(additionalData, getAdditionalData.apply(this, arguments));
					}
					return additionalData;
				}
			}),
			initFeature = function(grid, view) {
				view.previewExpanded = me.previewExpanded;

				// By this point, existing features are already in place, so this must be initialized and added
				view.featuresMC.add(feature);
				feature.init(grid);
			};

		// The grid has already created its view
		if (grid.view) {
			initFeature(grid, grid.view);
		}

		// At the time a grid creates its plugins, it has not created all the things
		// it needs to create its view correctly.
		// Process the view and init the RowBody Feature as soon as the view is created.
		else {
			grid.on({
				viewcreated: initFeature,
				single: true
			});
		}
	},

	/**
	 * Toggle between the preview being expanded/hidden on all rows
	 * @param {Boolean} expanded Pass true to expand the record and false to not show the preview.
	 */
	toggleExpanded: function(expanded) {
		var grid = this.getCmp(),
			view = grid && grid.getView(),
			bufferedRenderer = view.bufferedRenderer,
			scrollManager = view.scrollManager;

		if (grid && view && expanded !== view.previewExpanded ) {
			this.previewExpanded = view.previewExpanded = !!expanded;
			view.refreshView();

			// If we are using the touch scroller, ensure that the scroller knows about
			// the correct scrollable range
			if (scrollManager) {
				if (bufferedRenderer) {
					bufferedRenderer.stretchView(view, bufferedRenderer.getScrollHeight(true));
				} else {
					scrollManager.refresh(true);
				}
			}
		}
	}
});
/**
*	Ext Define
*		END
*/


/**
*	Ext Override
*		START
*/
/*Ext.override(Ext.draw.engine.ImageExporter, {
  defaultUrl: 'http://localhost:80/export'
});*/


Ext.override(Ext.ElementLoader, {
	statics: {
		Renderer: {
			Html: function(loader, response, active){
				loader.getTarget().update(response.responseText, active.scripts === true);
				return true;
			}
		}
	}
});

Ext.override(Ext.form.TextField, {
	validator:function(text){
		if(this.allowBlank==false && Ext.util.Format.trim(text).length==0)
		  return false;
		else
		  return true;
	}
});

Ext.override(Ext.toolbar.Toolbar, {
    layout: {
		overflowHandler: 'scroller' /*'Menu'*/
	}
});

/*Ext.override(Ext.Window, {
	constrain: true,
	constrainHeader: true
});*/

/*ExtJS panel container click : tree or grid select가 해제되지 않도록 함.*/
Ext.override(Ext.panel.Table,{
	initComponent: function() {
		var me = this;
		me.callParent(arguments);

		me.on('beforecontainerclick', function(){
			return false;
		});
	}
});


Ext.override(Ext.form.field.Display, {
	renderer	: function(value, x) {
		if(!x.multiline){
			return value;
		}

		//hack for displayfield issue in ExtJS 5
		var rtn = value.replace(/(\r\n|\n|\r)/g, "</br>");
		var html = [
			'<tbody style="padding:10px;line-height:200px;">',
				'<tr class="x-form-item-input-row">',
					'<td  valign="top" halign="left" width="' + x.labelWidth + '" class="x-field-label-cell">',
						'<label class="x-form-item-label x-unselectable x-form-item-label-left" style="width:' + x.labelWidth + 'px;margin-right:5px;" unselectable="on">' + x.getFieldLabel() +':</label>',
					'</td>',
					'<td class="x-form-item-body x-form-display-field-body " colspan="2">',
						'<div style="line-height:20px;" class="x-form-display-field" aria-invalid="false" data-errorqtip="">',
							rtn,
						'</div>',
					'</td>',
				'</tr>',
			'</tbody>'
		];

		x.on('afterrender',function(){
			 x.update(html.join(''));
		});
		return '';
	}
});


Ext.override(Ext.grid.column.Column, {
	draggable: false,
	sortable: true,
	menuDisabled: true
});


Ext.override(Ext.selection.CheckboxModel, {
	//mode:'SIMPLE'
	mode:'MULTI'
});


//Ext.override(Ext.grid.Panel, {
//	selModel:Ext.create('Ext.selection.CheckboxModel')
//});
/**
*	Ext Override
*		END
*/


/**
*	Ext Etc.
*		START
*/
var ajax_mask = true;
var mask_cnt = 0;
Ext.Ajax.on('beforerequest', function(connection,options){
	if(ajax_mask){
		mask_cnt++;

		if (Ext._bodyEl) {
			Ext.getBody().mask(Otm.com_msg_processing_data);
		}
	}
});

Ext.Ajax.on('requestcomplete', function(connection,options){
	if(ajax_mask){
		mask_cnt--;
		if(mask_cnt == 0){
			Ext.getBody().unmask();
		}
	}
});

Ext.Ajax.on('requestexception', function(connection,options){
	if(ajax_mask){
		mask_cnt--;
		if(mask_cnt == 0){
			Ext.getBody().unmask();
		}
	}
});

Ext.enableFx = false;
/**
*	Ext Etc.
*		END
*/