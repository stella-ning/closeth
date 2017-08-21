/**
 * Created by zjh on 2017/7/1 0001.
 */


var region = new Object();

region.isAdmin = false;

region.loadRegions = function(parent, type, target)
{
   // Ajax.call(region.getFileName(), 'type=' + type + '&target=' + target + "&parent=" + parent , region.response, "GET", "JSON");

    $.ajax({
        type : 'get',
        url : "index.php?module=behalf&act=set_shipping_area&t="+Math.random(),
        data : {type:type,target:target,parent:parent,operate:'sel_region'},
        dataType : 'json',
        success : function(res){
            if(res.status == 1){
//alert(res.result.regions);return;
                region.response (res.result, res.msg);
                // alert(res.result.html);

            }else if (res.status == -1){



            }else{
                showErrorMsg(res.msg);
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorMsg('网络失败，请刷新页面后重试');
        }
    })
}

function showErrorMsg(msg){

    alert(msg);
}

/* *
 * 载入指定的国家下所有的省份
 *
 * @country integer     国家的编号
 * @selName string      列表框的名称
 */
region.loadProvinces = function(country, selName)
{
    var objName = (typeof selName == "undefined") ? "selProvinces" : selName;

    region.loadRegions(country, 1, objName);
}

/* *
 * 载入指定的省份下所有的城市
 *
 * @province    integer 省份的编号
 * @selName     string  列表框的名称
 */
region.loadCities = function(province, selName)
{
    var objName = (typeof selName == "undefined") ? "selCities" : selName;

    region.loadRegions(province, 2, objName);
}

/* *
 * 载入指定的城市下的区 / 县
 *
 * @city    integer     城市的编号
 * @selName string      列表框的名称
 */
region.loadDistricts = function(city, selName)
{
    var objName = (typeof selName == "undefined") ? "selDistricts" : selName;

    region.loadRegions(city, 3, objName);
}

/* *
 * 处理下拉列表改变的函数
 *
 * @obj     object  下拉列表
 * @type    integer 类型
 * @selName string  目标列表框的名称
 */
region.changed = function(obj, type, selName)
{
    var parent = obj.options[obj.selectedIndex].value;

    region.loadRegions(parent, type, selName);
}

region.response = function(result, text_result)
{
    var sel = document.getElementById(result.target);

    sel.length = 1;
    sel.selectedIndex = 0;
    sel.style.display = (result.regions.length == 0 && ! region.isAdmin && result.type + 0 == 3) ? "none" : '';

    if (document.all)
    {
        sel.fireEvent("onchange");
    }
    else
    {
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent('change', true, true);
        sel.dispatchEvent(evt);
    }

    if (result.regions)
    {
        //for (i = 0; i < result.regions.length; i ++ )
        //{
        //    var opt = document.createElement("OPTION");
        //    opt.value = result.regions[i].region_id;
        //    opt.text  = result.regions[i].region_name;
        //
        //    sel.options.add(opt);
        //}

        for (i in  result.regions) {
            var opt = document.createElement("OPTION");
            opt.value = result.regions[i].region_id;
            opt.text  = result.regions[i].region_name;

            sel.options.add(opt);

            //document.getElementById(result.target).appendChild(opt);
        }
    }
}

region.getFileName = function()
{
    return "index.php?module=behalf&act=set_shipping_area";
}





 /*
 * 表单验证类
 *
 */

var Validator = function(name)
{
  this.formName = name;
  this.errMsg = new Array();

  /* *
  * 检查用户是否输入了内容
  *
  * @param :  controlId   表单元素的ID
  * @param :  msg         错误提示信息
  */
  this.required = function(controlId, msg)
  {
    var obj = document.forms[this.formName].elements[controlId];
    if (typeof(obj) == "undefined" || Utilstrim(obj.value) == "")
    {
      this.addErrorMsg(msg);
    }
  }
  

  /* *
  * 检查输入的内容是否是一个数字
  *
  * @param :  controlId   表单元素的ID
  * @param :  msg         错误提示信息
  * @param :  required    是否必须
  */
  this.isNumber = function(controlId, msg, required)
  {
    var obj = document.forms[this.formName].elements[controlId];
    obj.value = Utilstrim(obj.value);

    if (obj.value == '' && ! required)
    {
      return;
    }
    else
    {
      if ( ! UtilsisNumber(obj.value))
      {
        this.addErrorMsg(msg);
      }
    }
  }

  /* *
  * 检查输入的内容是否是一个整数
  *
  * @param :  controlId   表单元素的ID
  * @param :  msg         错误提示信息
  * @param :  required    是否必须
  */
  this.isInt = function(controlId, msg, required)
  {

    if (document.forms[this.formName].elements[controlId])
    {
      var obj = document.forms[this.formName].elements[controlId];
    }
    else
    {
      return;    
    }

    obj.value = Utilstrim(obj.value);

    if (obj.value == '' && ! required)
    {
      return;
    }
    else
    {
      if ( ! UtilsisInt(obj.value)) this.addErrorMsg(msg);
    }
  }

  /* *
  * 检查输入的内容是否是为空
  *
  * @param :  controlId   表单元素的ID
  * @param :  msg         错误提示信息
  * @param :  required    是否必须
  */
  this.isNullOption = function(controlId, msg)
  {
    var obj = document.forms[this.formName].elements[controlId];

    obj.value = Utilstrim(obj.value);

    if (obj.value > '0' )
    {
      return;
    }
    else
    {
      this.addErrorMsg(msg);
    }
  }

  
  this.passed = function()
  {
    if (this.errMsg.length > 0)
    {
      var msg = "";
      for (i = 0; i < this.errMsg.length; i ++ )
      {
        msg += "- " + this.errMsg[i] + "\n";
      }

      alert(msg);
      return false;
    }
    else
    {
      return true;
    }
  }

  /* *
  * 增加一个错误信息
  *
  * @param :  str
  */
  this.addErrorMsg = function(str)
  {
    this.errMsg.push(str);
  }
}




  function Utilstrim( text )
{
  if (typeof(text) == "string")
  {
    return text.replace(/^\s*|\s*$/g, "");
  }
  else
  {
    return text;
  }
}

function UtilsisNumber(val)
{
  var reg = /^[\d|\.|,]+$/;
  return reg.test(val);
}

function UtilsisInt(val)
{
  if (val == "")
  {
    return false;
  }
  var reg = /\D+/;
  return !reg.test(val);
}