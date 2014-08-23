<?php
/**
 * ldap连接
 * 
 */
class LDAP extends FormModel
{
    public $id;
	
	
	/**
	 * ldap连接，key、value键值对参考在最下面
	 */
	public function search($key, $value)
	{
		$ldaphost = "master.ldap.ebupt.com";  
		$ldapport = 389;                 
		$ds = ldap_connect($ldaphost, $ldapport)or die("Could not connect to $ldaphost");
		
		$dn = "";
		$dnpwd = "";
		$base = "ou=staff,dc=ebupt,dc=com";
		
		if($ds){
			$r = ldap_bind($ds, $dn ,$dnpwd);  //匿名绑定
			$sr = ldap_search($ds, $base, $key."=".$value);  
			$info = ldap_get_entries($ds, $sr);
			
			//简化数据
			if(isset($info[0])) {
				$info = $info[0];
			} else {
				return null;
			}
			if(!$info) {
				ldap_close($ds);  //关闭连接
				return null;
			}
			
			foreach($info as &$record) {
				$record = $record[0];
			}
			
			ldap_close($ds);  
			
			return $info;
		} else {
			return false;
		}
	}
	
	/**
     * ldap所取数据键值对关联	
	 *
		st 工作省份
		l 工作城市
		hiredate 入职时间 2012-02-17
		birthdate 生日 03-03
		modifytimestamp 修改时间时间戳
		modifiersname 
		postalcode 邮编
		postaladdress 邮寄地址
		telephonenumber 座机号
		manager 上级领导
		department 部门
		welfarelocation
		title 职称
		seatno 座位号
		jobcode
		telephone-office2
		telephone-assistant
		physicaldeliveryofficename
		pager
		mobile 手机号
		mail 邮箱
		givenname
		gender
		facsimiletelephonenumber
		employeenumber 工号
		displayname 姓名
		band  级别
		uid 
		sn
		cn
		gidnumber
		homedirectory
		objectclass
		dialupaccess
		mozillanickname
		loginshell
		radiusauthtype
		radiusidletimeout
		radiussessiontimeout
		company 公司
		oasynced
		jpegphoto 照片
		uidnumber
		creatorsname
		createtimestamp
		count
	*/	
}
