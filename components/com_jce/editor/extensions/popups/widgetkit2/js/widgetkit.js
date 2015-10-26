/* JCE Editor - 2.5.11 | 26 October 2015 | http://www.joomlacontenteditor.net | Copyright (C) 2006 - 2015 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
(function(){WFPopups.addPopup('widgetkit2',{params:{lightbox_keyboard:'',lightbox_duration:'',lightbox_group:''},setup:function(){$.each(this.params,function(k,v){$('#widgetkit_'+k).val(v);});},check:function(n){return n.getAttribute('data-lightbox')||n.getAttribute('data-uk-lightbox');},remove:function(n){tinyMCEPopup.editor.dom.setAttribs(n,{'data-lightbox':null,'data-uk-lightbox':null,'data-lightbox-type':null});},str2json:function(str,notevil){try{if(notevil){return JSON.parse(str.replace(/([\$\w]+)\s*:/g,function(_,$1){return'"'+$1+'":';}).replace(/'([^']+)'/g,function(_,$1){return'"'+$1+'"';}));}else{return(new Function("","var json = "+str+"; return JSON.parse(JSON.stringify(json));"))();}}catch(e){return false;}},convertData:function(s){if(s.indexOf('{')===0){var start=(s?s.indexOf("{"):-1),options={};if(start!=-1){try{options=this.str2json(string.substr(start));}catch(e){}}
return options;}else{var a=[];$.each(s.split(';'),function(i,n){if(n){n=n.replace(/([\w]+):(.*)/,'"$1":"$2"');a.push(n);}});return $.parseJSON('{'+a.join(',')+'}');}},getAttributes:function(n){var ed=tinyMCEPopup.editor,args={};var data=ed.dom.getAttrib(n,'data-lightbox')||ed.dom.getAttrib(n,'data-uk-lightbox');if(data&&data!=="on"){data=this.convertData(data);$.each(data,function(k,v){$('#widgetkit_lightbox_'+k).val(v);});}
$('#widgetkit_lightbox_title').val(ed.dom.getAttrib(n,'title'));var map=WFPopups.config.map||{'href':'src'};$.each(map,function(from,to){var href=ed.dom.getAttrib(n,from);href=href.replace(/(\?|&)tmpl=component/i,'');$('#'+to).val(href);args.src=href;});return args;},setAttributes:function(n,args){var self=this,ed=tinyMCEPopup.editor,data=[];this.remove(n);tinymce.each(['group','keyboard','duration'],function(k){var v=$('#widgetkit_lightbox_'+k).val();if(v==''||v==null){if(args[k]){v=args[k];}else{return;}}
data.push(k+':'+v);});if(args.data){$.each(args.data,function(k,v){data.push(k+':'+v);});}
var src=ed.dom.getAttrib(n,'href');if(/index\.php/.test(src)&&/:\/\//.test(src)===false){if(/\?/.test(src)){src+='&tmpl=component';}else{src+='?tmpl=component';}
ed.dom.setAttrib(n,'href',src);}
var value="on";if(data.length){value=$.map(data,function(s){var v=s.split(':');return"{"+v[0]+":'"+v[1]+"'}";}).join(',');}
var type=$('#widgetkit_lightbox_type').val();if(type){ed.dom.setAttrib(n,'data-lightbox-type',type);}
ed.dom.setAttrib(n,'data-uk-lightbox',value);ed.dom.setAttrib(n,'title',$('#widgetkit_lightbox_title').val());ed.dom.setAttrib(n,'target','_blank');},onSelect:function(){},onSelectFile:function(args){}});})();