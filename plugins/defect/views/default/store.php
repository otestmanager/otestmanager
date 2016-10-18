<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>

<script type="text/javascript">
var defect_code_store = Ext.create('Ext.data.Store', {
    fields:['pco_seq', 'pco_type','pco_name','pco_is_required','pco_is_default','pco_default_value'],
    proxy: {
        type	: 'ajax',
		url		: './index.php/Plugin_view/defect/code_list',
		extraParams: {
			project_seq : <?=$project_seq?>
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

var defect_store = Ext.create('Ext.data.Store', {
    fields:[
		'df_seq', 'df_subject','df_status','df_severity','df_priority','df_frequency',
		'df_id',
		'df_assign_member','writer_name'
	],
    pageSize: 50,
	proxy: {
        type	: 'ajax',
		url		: './index.php/Plugin_view/defect/defect_list',
		extraParams: {
			project_seq : <?=$project_seq?>
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

var defect_contact_store = Ext.create('Ext.data.Store', {
	fields:['mb_email','mb_name'],
    proxy: {
		type	: 'ajax',
		url		: './index.php/Project_setup/project_userlist',
		extraParams: {
			pr_seq : <?=$project_seq?>
		},
		actionMethods : {
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

var defect_customform_store = Ext.create('Ext.data.Store', {
	fields:['pc_seq','pc_name','pc_is_required','pc_formtype','pc_default_value','pc_content','pc_is_use'],
    proxy: {
		type	: 'ajax',
		url		: './index.php/Project_setup/userform_list',
		extraParams: {
			pr_seq		: <?=$project_seq?>,
			pc_category : 'ID_DEFECT',
			pc_is_use	: 'Y'
		},
		actionMethods : {
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

var defect_status_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_name'],
    data:{'items':[]},
    proxy: {
        type: 'memory',
        reader: {
            type: 'json',
            rootProperty: 'items'
        }
    }
});

var defect_severity_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_name'],
    data:{'items':[]},
    proxy: {
        type: 'memory',
        reader: {
            type: 'json',
            rootProperty: 'items'
        }
    }
});

var defect_priority_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_name'],
    data:{'items':[]},
    proxy: {
        type: 'memory',
        reader: {
            type: 'json',
            rootProperty: 'items'
        }
    }
});

var defect_frequency_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_name'],
    data:{'items':[]},
    proxy: {
        type: 'memory',
        reader: {
            type: 'json',
            rootProperty: 'items'
        }
    }
});

var defect_status_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_name'],
    data:{'items':[]},
    proxy: {
        type: 'memory',
        reader: {
            type: 'json',
            rootProperty: 'items'
        }
    }
});

var defect_searchField_store = new Ext.data.SimpleStore({
	 fields:['Key', 'Name']
	,data:[['subject', Otm.com_subject],['description', Otm.com_description],['sub_des', Otm.com_subject+'+'+Otm.com_description],['writer', Otm.com_creator],['charge', Otm.com_user]]
});
</script>