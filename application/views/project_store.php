<script type="text/javascript">
var plugin_category_store = [];
</script>
<?php
/*
*	플러그인 정보로 기본 데이터 그룹 생성
*/
foreach ($migrations as $plugin=>$v) {
	//echo json_encode($v);
	//service_id,name,current_ver,iconcls,subpage,order,userform,ishidden,migration_ver,description
	//echo $v['service_id'].' : ';
	//echo $v['categoryid'].' : ';
	//echo $v['userform'].' : ';
	//echo $v['name'].' : ';
	//echo $plugin;


	if($v['ishidden'] != 'true' && $v['userform'] && $v['userform'] !=='' && $v['userform'] == 'true'){
		echo '<script type="text/javascript">';
		echo "plugin_category_store.push({boxLabel : '".$v['name']."', name : 'category', inputValue : '".$v['categoryid']."', checked: ".(($plugin == '0')?'true':'false')."})";
		echo '</script>';
	}
}

?>
<script type="text/javascript">
var projectlist_store = Ext.create('Ext.data.Store', {
	fields:['pr_seq','pr_name','pr_startdate','pr_enddate','writer','regdate',{name:'user_cnt',type:'integer'},{name:'defect_cnt',type:'integer'},{name:'defect_cnt_close',type:'integer'}],
	pageSize: 50,
	proxy: {
		type: 'ajax',
		url:'./index.php/Otm/project_list',
		extraParams: {
		},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	},
	autoLoad:true
});

var project_view_store = Ext.create('Ext.data.Store', {
	fields:['pr_seq','pr_name','pr_description','pr_startdate','pr_enddate','writer','regdate','last_writer','last_update'],
	proxy: {
		type: 'ajax',
		url:'./index.php/Otm/project_view',
		extraParams: {
		},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	},
	autoLoad:false
});

var project_tree_store = Ext.create('Ext.data.TreeStore', {
	root: {
		text: 'Root',
		expanded: true,
		children: []
	},
	proxy: {
		type: 'ajax',
		url:'./index.php/Otm/project_tree_list',
		extraParams: {
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

var permission = Ext.create('Ext.data.Store', {
	fields:['pmi_category','pmi_name','pmi_value'],
	proxy: {
		type: 'ajax',
		url:'./index.php/Otm/permission_list',
		extraParams: {
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