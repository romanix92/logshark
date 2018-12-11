var search_table= [
	
		{
		  regex:/CM-STATUS event (\d+) on DS channel (\d+) for CM (\S+)/gm, 
		  content:"CM-STATUS($1)",
		  title: "CM-STATUS($1) DS: $2<br>$3",
		  group: "$3",
		},
		
		
		{
		  disabled:true,
		  regex:/DhcpHandler::HandleUsDhcpv6Pdu:/gm, 
		  content:"dhcpv6", 
		  title:"dhcpv6",
		  group:"main"
		},
		
		{
		  disabled: true,
		  regex:/RegSm - ReplicateUpdate for (\S+) replObj  exists/gm, 
		  content:function(i, m){return "ReplicateUpdate";}, 
		  title:function(i,m){
		  
		  AddCmMac(m[1]);
		  i.group=m[1];
		  
		  return m[1];
		  }
		},
		
		
		{
		  regex:"RegSm_::ProcessAddCpe: add CPE (\\S+) behind modem (\\S+)", 
		  content:"+CPE:$1", 
		  title:"CPE:$1<br>CM:$2",
		  group:"$2",
		},
		
		
		
	
		
		
	
	];