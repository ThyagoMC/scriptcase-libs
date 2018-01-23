<?php
function initDynBlock($fieldRef, $fieldGroup, $blockHeader, $errorTime = 5000){
	$appName = $this->Ini->nm_cod_apl;
	if($this->NM_ajax_flag || $this->nmgp_opcao == 'formphp') return;
	$_SESSION['__dynBlock__'][$fieldRef] = array('fieldGroup'=>$fieldGroup);
?>
<style>
	inputmask{display:none}
	.no_rem_link .rem_link {  display: none; }
	.no_add_link .add_link { display: none; }
	.flr{ float:right }
	.btn_link{ margin: 4px; }
	.hdivtgt{ margin: -2px -2px 0; border: 2px solid #719c54 }
	.divtgt{ padding: 2px; border: 1px solid #AAA; }
</style>
<script>
	$(document).ready(function(){
		window.dynBlock = {};
		dynBlock['<?=$fieldRef?>'] = (function(){
			var fieldRef = '<?=$fieldRef?>';
			var txtHeader = '<?=$blockHeader?>';
			var fieldGroup = '<?=$fieldGroup?>';
	
			var divTarget = $('[name='+fieldRef+']').parents('div[id*=div_hidden]');
			var size = divTarget.find('.scFormBlock').attr('colspan');
			if(!size)size = divTarget.find('tr:first').children().length;
			var trbtn ="<tr><td colspan='"+size+"'><div class='flr'><a> </a><a href='javascript:;' class='btn_link add_link'>Adicionar</a>";
			trbtn += "<a href='javascript:;' class='btn_link rem_link'>Remover</a></td></tr>";
	
			divTarget.prepend('<div class="scFormBlock hdivtgt">'+txtHeader+'</div>');
	
			divTarget.addClass('divtgt');
			var blockInit = divTarget.children('table:first')
			blockInit.append(trbtn);
			var blockContent = blockInit.clone();
			blockInit.css('display', 'none');
			$(document.F1).prepend("<div id='hidInit' style='display:none'></div>");
			$('#hidInit').prepend(blockInit);
			var arrEvtG = [];
			blockContent.find('input,select,textarea').each(function(){ 
				var n = $(this).attr('name');
				var fieldOrig = $(blockInit).find('[name='+n+']');
				var d = fieldOrig.data();
				if(typeof d == "object" && d.events){ 
					for(var nEvt in d.events){ 
						var arrEv = d.events[nEvt+''];
						for(var i in arrEv){ 
							var ev = arrEv[i+''];
							var str = ev.handler.toString();
							if (str.search(/sc_<?=$appName?>_/) > -1){ 
								arrEvtG[n+''] = arrEvtG[n+''] || {};
								arrEvtG[n+''][nEvt+'']=1;
							}
						}
					}
				}
				$(this).attr('name', n+"_#");
			});
			blockContent.addClass('gfields');
	
			function addGroup(block){
				var next = addGroup.next++;
				var toAdd = block || blockContent.clone();
				divTarget.append(toAdd);
				toAdd.find('input,select,textarea').each(function(){ 
					var n = $(this).attr('name');
					$(this).attr('name', n+next);
					var nOrig = n.replace(/_#.*/,'');
					if(arrEvtG[nOrig+'']){
						for(var evt in arrEvtG[nOrig+'']){
							(function(_elm, _evt, _name){
								$(_elm).bind(_evt, function(){ 
									getAll();
									$('[name='+_name+']')[_evt+'']();
								});
							})(this, evt, nOrig);
						}
					}
				});
				toAdd.find("inputmask").each(function(){ 
					var elem = $(this).parent().find("span input:text");
					try{
						var mask = eval("({"+$(this).text()+"})"); 
					}catch(e){
						var mask = eval("("+$(this).text()+")"); 
					}
					elem.inputmask(mask);
				});
				controlLinks();
			}
			addGroup.next =1;
			
			function controlLinks(){
				var gfs = divTarget.find(".gfields");
				gfs.addClass('no_add_link');
				divTarget.find(".gfields:last").removeClass('no_add_link');
				if(gfs.length == 1){
					 gfs.addClass('no_rem_link');
				}else{
					gfs.removeClass('no_rem_link');
				}
			}
			
			function removeGroup(e){
				$(e.target).parents('.gfields:first').remove();
				setTimeout ( function (){ 
					controlLinks();
				} , 120);
			}
			divTarget.delegate('.rem_link', 'click', removeGroup);
			divTarget.delegate('.add_link', 'click', function(){addGroup();});
			
			function __doParse(str){
				str = $.trim(str).replace(/\\+/g, '\\').replace(/\\"/g,'"');
				if (str != ""){ 
					try{ 
						jp = JSON.parse(str);
					}catch(e){ 
						jp = JSON.parse('"'+str+'"');
					}
				}else{ 
					jp = [];
				}
				if(typeof jp == "string"){ 
					try{ 
						jp = JSON.parse(jp);
					}catch(e){ 
						jp = JSON.parse('"'+jp+'"');
					}
				}
				return jp;
			}
	
			function loadAll(){
				var jp =  __doParse( $('[name='+fieldGroup+']').val() );
				if(jp.length > 0){
					divTarget.find('.gfields').remove();
					for(var i in jp){
						hasVals = true;
						var group = jp[i+''];
						console.log(group);
						var toAdd = blockContent.clone();
						for(var p in group){
							var field = toAdd.find('[name*='+p+'_#]');
							if (field.is(':radio, :checkbox')){
								field.each(function(){
									var  vf = $(this).val();
									var vc = group[p+''];
									if(vf == vc){
										$(this).attr('checked', true);
									}
								});
							}else{
								field.val(group[p+'']);
							}
							if(p.search("_error") > -1){
								var ep = p.replace("_error", "");
								field = toAdd.find('[name*='+ep+'_#]');
								toAdd.find('[id=id_error_display_'+ep+'_frame]').show();
								toAdd.find('[id=id_error_display_'+ep+'_text]').text(group[p+'']);
								field.addClass('scFormInputError');
								(function (_toAdd, _ep, _field){ 
									setTimeout(function(){ 
										_toAdd.find('[id=id_error_display_'+_ep+'_frame]').hide();
										_field.removeClass('scFormInputError');
									}, <?=$errorTime?>);
								})(toAdd, ep, field);
							}
						}
						addGroup(toAdd);
					}
				}else{
					addGroup(); 
				}
				if(divTarget.find('>table').is(':hidden')){
					divTarget.hide();
				}else{
					divTarget.show();
				}
			}
			loadAll();
	
			function getAll(){
				var arrAllGP = [];
				divTarget.find('.gfields').each(function(){
					arrAllGP.push($(this).find('input,textarea,select').serializeArray());
				});
				var finalObj = [];
				for(var i in arrAllGP){
					var obj = arrAllGP[i+'']; 
					var midObj = {};
					for(var p in  obj){
						var pair = obj[p+''];
						var name = pair['name'].replace(/_#.*/, '');
						midObj[name+''] = pair['value'];
					}
					finalObj.push(midObj);
				}
				$('[name='+fieldGroup+']').val(JSON.stringify(finalObj));
			}
	
			var next_nm_atualiza = nm_atualiza;
			nm_atualiza = function (x, y){
				getAll();
				next_nm_atualiza(x, y);
			}
	
			var next_nm_recarga_form = nm_recarga_form;
			nm_recarga_form = function (x, y){
				getAll();
				next_nm_recarga_form(x,y);
			}
			
			return loadAll;
		})();
	});
</script>
<?php
}

function arrDynBlock($content){
	$arr  = json_decode($content ,true) ;
	if($arr == null){
		$content = str_replace('\\"','"',$content );
		$arr  = json_decode($content ,true) ;
	}
	return $arr;
}
function jsonDynBlock($data){
	return json_encode(__jsonAuxDynBlock($data));
}

function __jsonAuxDynBlock ($data){
	if(is_array($data)){
		foreach ($data as &$item){
			$item = __jsonAuxDynBlock ($item);
		}
	}else if (is_string($data)){
		if(!mb_detect_encoding($data, 'UTF-8', true)){
			$data = utf8_encode($data);
		}
	}
	return $data;
}
function reloadDynBlock($fieldRef, $data=false){
	if($data){
		$fiedlGroup = $_SESSION['__dynBlock__'][$fieldRef]['fieldGroup'];
		$jsonData = jsonDynBlock($data);
		$this->$fiedlGroup = $jsonData ;	
	}
	if($this->NM_ajax_flag){
		sc_ajax_javascript("window.dynBlock.".$fieldRef);
	}	
}
?>