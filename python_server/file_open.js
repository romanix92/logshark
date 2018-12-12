function RequestFileList(path) {
	$.ajax({
        type: "POST",
        data: result,
        url: "api?action=list_dir&file="+path,
        success: function(result) {
          var list = JSON.parse(result);
          for (var i in list.files) {
             var file = list.files[i];
          }
        },
      });
}