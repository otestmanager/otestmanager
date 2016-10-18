<script type="text/javascript">
function event_controller(Obj)
{
	this.event;
	this.xtype;
	this.objid;
}
getter = {
	load:{
	},
	view:{
	},
	data:{
	},
	panel: function(obj){
		var closable = true;
		if(obj.id == 'comtc_main'){
			closable = false;
		}

		return {
			layout:'fit',
			xtype: 'panel',
			title: obj.title,
			id: obj.id,
			closable: closable,
			plain: true,
			scope:this,
			loader: {
				autoLoad:true,
				loadMask: true,
				scripts: true,
				url :'./index.php/'+obj.url
			},
			listeners:{
				render: function(tab){
				},
				activate : function(tabpanel){
				}
			}
		}
	}
};

setter = {
	panel: function(obj){
		var panel = Ext.getCmp(obj.target);
		var tabIndex = panel.items.findIndex('id', obj.id);
		if(Ext.getCmp(obj.id) && tabIndex != -1){
			panel.setActiveTab(tabIndex);
		}else{
			Ext.getCmp("acc").mask(Otm.com_msg_processing_data);
			panel.add(getter.panel(obj)).show();
			Ext.getCmp("acc").unmask();
		}
	}
};

OTM = {
	listeners:{
		itemclick : function(view,rec,item,index,eventObj) {
			var before_pr = Ext.getCmp('before_select_project_tree').getValue();

			if(before_pr){
				Ext.getCmp('before_select_project_tree').setValue('system_setup');

				var tabPanel = Ext.getCmp('main_tab');
				var tab_cnt = tabPanel.items.length;

				var default_tab_cnt = 1 + dashboard_key;
				// 기본 Tab Panel 추가
				//default_tab_cnt = 2;

				for(var i=tab_cnt-1; i>=default_tab_cnt; i--){
					tabPanel.remove(tabPanel.items.getAt(i));
				}
				tabPanel.doLayout();

			}else{
				Ext.getCmp('before_select_project_tree').setValue('system_setup');
			}

			if(rec.get('type')){
				setter.panel({target:'main_tab',title:rec.get('text'),id:rec.get('type'),url:'System_setup/'+rec.get('type')});
			}
		},
		afterrender: function(thisCmp, eOpts){
			if(this.id == 'acc'){
			}
		},
		expand: function(thisCmp) {
			switch(thisCmp.id){
				case "comtc_acc":
					Ext.getCmp('before_select_project_tree').setValue('comtc');
					var tabPanel = Ext.getCmp('main_tab');
					var tab_cnt = tabPanel.items.length;

					for(var i=tab_cnt-1; i>=1; i--){
						tabPanel.remove(tabPanel.items.getAt(i));
					}
					tabPanel.doLayout();

					var url = 'Com_testcase';
					setter.panel({target:'main_tab',title:thisCmp.title,id:'comtc_main',url:url});
					break;
			}
		}
	}
};
</script>
