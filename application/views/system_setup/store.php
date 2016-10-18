<script type="text/javascript">
var user_store = Ext.create('Ext.data.Store', {
	fields:['mb_seq','mb_email','mb_name','mb_pw','mb_tel','mb_is_admin','mb_is_approved','writer','regdate','last_writer','last_update'],
	pageSize: 50,
	proxy: {
		type	: 'ajax',
		url		: './index.php/User/userlist',
		actionMethods : {
			create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
		},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	},
	autoLoad:true
});

var group_store = Ext.create('Ext.data.Store', {
	fields:['gr_seq','gr_name',{name:'gr_user_cnt',type:'integer'},'gr_content','writer','regdate','last_writer','last_update'],
	proxy: {
		type	: 'ajax',
		url		: './index.php/Group/grouplist',
		extraParams: {
		},
		actionMethods: {
			create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
		},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	}
});

var group_user_store = Ext.create('Ext.data.Store', {
	fields:['mb_name','mb_email'],
	proxy: {
		type: 'ajax',
		url:'./index.php/Group/group_userlist',
		extraParams: {
		},
		actionMethods: {
			create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
		},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	}
});

var role_store = Ext.create('Ext.data.Store', {
	fields:['rp_seq','rp_name','writer','regdate','permission_data'],
	proxy: {
		type: 'ajax',
		url:'./index.php/Role/role_list',
		extraParams: {
		},
		actionMethods: {
			create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
		},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	},
	autoLoad:false
});

var status_store = Ext.create('Ext.data.Store', {
	fields:['co_seq','co_name','val'],
	proxy: {
		type: 'ajax',
		url:'./index.php/Code/code_list_workflow/status',
		extraParams: {
		},
		actionMethods: {
			create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
		},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	},
	autoLoad:false
});
</script>