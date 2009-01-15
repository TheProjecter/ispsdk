var lastOpenedMenu = null;  //указатель последнего открытого элемента меню

var menuR;  //указатель на контекст для выполнения запросов (результат сохраняется в resultDiv)
var jsonR;  //указатель на контекст для выполнения запросов JSON
var resultDiv;      //указатель на DIV элемент, в который сохраняется результат выполнения запроса
var closeMenuTimer; //таймер, который используется для отслеживания закрытия меню при нажатии в BODY, а не по меню
var timeToClose = 100;  //время закрытия для таймера меню
var lastopenedDiv = null; //последний развернутый элемент меню
var menuCADialogTimer = null; //таймер, для правильной работы menuCADialog
var isadmin = true;  //переменная хранящая статус администратор ли пользователь


//массив функций для каждого класса объекта навешивающий события
var fwsr_events = new Array(); 
fwsr_events['event.FWSRSelectMenu'] = function(e) {
  postobject[e.id] = e.getElementsByTagName('select').item(0).value;
  e.getElementsByTagName('select').item(0).onchange = function(event) {
    postobject[e.id] = this.value;
    MakeAJAXUpdate(e.id);
  }
}

function PObj() {
}
var postobject = new PObj();

function ShowMist(loading) {
  $('mist').style.visibility = 'visible';
  if (loading) {
    $('mist').addClass('loadingbg');
  }
}

function HideMist() {
  $('mist').style.visibility = 'hidden';
  $('mist').removeClass('loadingbg');
}

function MakeAJAXUpdate(elname) {
  elname = elname.substr(5,elname.length-5);
  ShowMist(true);
  var r = new Request.HTML({
    url: 'ajaxer.php?page='+pagenumber+'&elem='+elname,
    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
      HideMist();
      var els = responseTree[0].getElementsByTagName('form').item(0).childNodes;
      for(var i = 0; i < els.length; i++) {
        if (els[i].nodeName == 'DIV') {     
          $(els[i].id).set('html',els[i].get('html'));
        }
      }
      
    }
  }).post(postobject);
}

function showYesNo(text, yes, no) {
  $('yesnoDialog_message').set('text', text);
  $('yesnoDialog').style.left = (document.body.clientWidth - $('yesnoDialog').clientWidth)/2 + 'px';
  ShowMist();
  $('yesnoDialog').style.visibility = 'visible';
  $('yesnoDialog_yes').onclick = function() {
    $('yesnoDialog').style.visibility = 'hidden';
    HideMist();
    if (yes) yes();   
  }
  $('yesnoDialog_no').onclick = function() {
    $('yesnoDialog').style.visibility = 'hidden';
    HideMist();
    if (no) no();   
  }
}

function ShowMessage(text) {
  $('messageDialog_message').set('text', text);
  $('messageDialog').style.left = (document.body.clientWidth - $('messageDialog').clientWidth)/2 + 'px';
  ShowMist();
  $('messageDialog').style.visibility = 'visible';
  $('messageDialog_ok').onclick = function() {
    $('messageDialog').style.visibility = 'hidden';
    HideMist();
  }
}

//функция для отображения сообщения m
function Message(m) {
  $('messages').set('text',m);
}

//функция для закрытия меню (используется только таймером)
function closeMenu() {
  if (lastopenedDiv == null) return ;
  while(lastopenedDiv != $('menu0')) {
    lastopenedDiv.style.display = 'none';
    lastopenedDiv = lastopenedDiv.parentNode.parentNode;
  }
  lastopenedDiv = null;
}

function eventsOnSM(el) {
  $(el).addEvent('click',function() {
    loadSubMenu(this);
  });
  if (isadmin) {
    $(el).addEvent('mouseover',function(e) {
      $clear(menuCADialogTimer);
      menuCADialogTimer = menuCADialog_show.delay(timeToClose,this);        
    });
    $(el).addEvent('mouseout',function() {
      menuCADialogTimer = menuCADialog_hide.delay(timeToClose,this);
    });
    $(el).addEvent('mousedown',function() {
      this.dragStartPause = menuElementDrag.delay(500,this);          
    });
    $(el).addEvent('mouseup', function() {
      $clear(this.dragStartPause);
    });
  }
}

function eventsOnMI(el) {
  $(el).addEvent('click',function() {loadPage(this);});
  if (isadmin) {
    $(el).addEvent('mouseover',function(e) {
      $clear(menuCADialogTimer);
      menuCADialogTimer = menuCADialog_show.delay(timeToClose,this);        
    });
    $(el).addEvent('mouseout',function() {
      menuCADialogTimer = menuCADialog_hide.delay(timeToClose,this);
    });
  }
}

//функция "навешивания" событий onclick для загруженного меню
function applyOnClickMenu(rd) {
  var as = rd.getElementsByTagName('a');      
  for(var i = 0; i < as.length; i++) {
    if (as[i].parentNode.className == 'sm') {
      eventsOnSM(as[i]);
    } else if (as[i].parentNode.className == 'mi') {
      $(as[i]).addEvent('click',function() {loadPage(this);});
      if (isadmin) {
        $(as[i]).addEvent('mouseover',function(e) {
          $clear(menuCADialogTimer);
          menuCADialogTimer = menuCADialog_show.delay(timeToClose,this);        
        });
        $(as[i]).addEvent('mouseout',function() {
          menuCADialogTimer = menuCADialog_hide.delay(timeToClose,this);
        });
      }
    } else if (as[i].parentNode.className == 'smi') {          
      if (as[i].className == 'new_file') {
        $(as[i]).addEvent('click',function() {addNewFile(this);});
      } else {
        $(as[i]).addEvent('click',function() {addNewMenu(this);});
      }
    }
  }
}

//функция инициализации (здесь инициализируется контексты запросов, закрытие меню,
window.addEvent('domready', function() {
  menuR = new Request.HTML({method: 'post', url:'ajaxer.php?usna=mm', 
    onSuccess: function(html) {
      resultDiv.set('text', '');
      resultDiv.adopt(html);
      applyOnClickMenu(resultDiv);
      HideMist();
    },
    onFailure: function() {
      Message('The request failed.');
    }
  });

  jsonR = new Request.JSON({method: 'post', url:'ajaxer.php?usna=mm',
    onFailure: function() {
      Message('The request failed.');
    }
  });

  document.body.onclick = function() {
    closeMenuTimer = window.setTimeout("closeMenu()",timeToClose);
  };

  applyOnClickMenu($('menu0'));
  $('mist').addEvent('click', function() {
    window.setTimeout("window.clearTimeout(closeMenuTimer)",timeToClose / 2);
  });

  $('menuElementDialog').addEvent('click', function() {
    window.setTimeout("window.clearTimeout(closeMenuTimer)",timeToClose / 2);
  });

  $('menuCADialog').addEvent('mouseover',function() {
    $clear(menuCADialogTimer);
  });

  $('menuCADialog').addEvent('mouseout',function() {
    menuCADialogTimer = menuCADialog_hide.delay(timeToClose,this);
  });

  for(it in fwsr_events) {
    if (it.toString().substr(0,6) == 'event.') {
      var es = $$('div.'+it.toString().substr(6,it.toString().length-6));
      for(var i = 0; i < es.length; i++)
        fwsr_events[it](es[i]);
    } 
  }

});


//загрузка подменю (здесь же должны обрабатываться клики на удаление\изменение меню)
function loadSubMenu(e) {
  var m = parseInt(e.parentNode.getAttribute('menu.id'));
  window.setTimeout("window.clearTimeout(closeMenuTimer)",timeToClose / 2);
  var div;
  var divs = e.parentNode.getElementsByTagName('div');
  if (divs.length > 0) {
    div = divs[0];
  } else {
    div = document.createElement('div');
    div = $(div);
    e.parentNode.appendChild(div);  
    resultDiv = div;
    ShowMist(true);
    menuR.send('ml='+m);
  }
  if (div == lastopenedDiv) {
    if (div.style.display == 'none') div.style.display = 'block';
    else div.style.display = 'none';
  } else {
    if (lastopenedDiv != null) {
      while( div.parentNode.parentNode != lastopenedDiv) {
        lastopenedDiv.style.display = 'none';
        lastopenedDiv = lastopenedDiv.parentNode.parentNode;
      }
    } 
    div.style.display = 'block';
    div.style.width = '200px';
    div.style.left = (e.parentNode.offsetLeft + 20) + 'px';
    lastopenedDiv = div;
  }
}

//реализация кода на диалоге menuElement при нажатии кнопки OK
function menuElementDialog_ok() {
  $('menuElementDialog').Submitter(
    $('menuElementDialog.m').value, 
    $('menuElementDialog.f').value, 
    $('menuElementDialog.g').value, 
    $('menuElementDialog').menuID,
    $('menuElementDialog').domel);
  $('menuElementDialog').style.visibility = 'hidden';
  HideMist();
}

//реализация кода на диалоге menuElement при нажатии кнопки Cancel
function menuElementDialog_cancel() {
  $('menuElementDialog').style.visibility = 'hidden';
  HideMist();
}

//инициализация диалога menuElement
function menuElementDialog(m, f, g, s, menuid, e) {
  $('menuElementDialog').Submitter = s;
  $('menuElementDialog').menuID = menuid;
  $('menuElementDialog').domel = e.parentNode;
  $('menuElementDialog.m').value = m;
  $('menuElementDialog.f').value = f;
  $('menuElementDialog.g').value = g;
  $('menuElementDialog').style.left = (document.body.clientWidth - $('menuElementDialog').clientWidth)/2 + 'px';
  $('menuElementDialog').style.top = 40 + 'px';
  ShowMist();
  $('menuElementDialog').style.visibility = 'visible';
}

//субмиттер (подтверждение) добавления нового файла (выполняется в функции menuElementDialog_ok)
function addNewFileSubmitter(m, f, g, menuid,e) {
  var div = document.createElement('div');
  $(div).set('class','mi');
  var a = document.createElement('a');
  eventsOnMI(a);
  a.appendChild(document.createTextNode(m));
  div.appendChild(a);
  e.parentNode.insertBefore(div,e);
  div.setAttribute('menu.name',m);
  div.setAttribute('menu.fname',f);
  div.setAttribute('menu.group',g);

  jsonR.onSuccess = function(json) {
    div.setAttribute('menu.id',json.newid);
    a.setAttribute('href','?page='+json.newid);
    Message(json.message);
  };
  jsonR.send('addFile=&m='+m+'&f='+f+'&g='+g+'&menuid='+menuid);
}

//субмиттер (подтверждение) добавления нового меню (выполняется в функции menuElementDialog_ok)
function addNewMenuSubmitter(m, f, g, menuid,e) {
  var div = document.createElement('div');
  jsonR.onSuccess = function(json) {
    div.setAttribute('menu.id',json.newid);
    Message(json.message);
  };
  jsonR.send('addMenu=&m='+m+'&f='+f+'&g='+g+'&menuid='+menuid);  

  $(div).set('class','sm');
  div.setAttribute('menu.name',m);
  div.setAttribute('menu.fname',f);
  div.setAttribute('menu.group',g);
  var a = document.createElement('a');
  eventsOnSM(a);
  a.setAttribute('href','javascript:;');

  a.appendChild(document.createTextNode(m));
  div.appendChild(a);
  e.parentNode.insertBefore(div,e);
}

//выполнение диалога добавления нового файла
function addNewFile(e) {  
  window.setTimeout("window.clearTimeout(closeMenuTimer)",timeToClose / 2);
  menuElementDialog('','',0, addNewFileSubmitter, parseInt(e.parentNode.parentNode.parentNode.getAttribute('menu.id')), e);
}

//выполнение диалога добавления нового меню
function addNewMenu(e) {  
  window.setTimeout("window.clearTimeout(closeMenuTimer)",timeToClose / 2);
  menuElementDialog('','',0, addNewMenuSubmitter, parseInt(e.parentNode.parentNode.parentNode.getAttribute('menu.id')), e);
}

//загрузка страницы
function loadPage(el) {
  //alert(el.parentNode.getAttribute('menu.id'));
}

//диалог authDialog
//функция отображения диалога в режиме "логин"
function authDialog_login_show() {
  if ($('authDialog_login').style.display == 'block') $('authDialog_login').style.display = 'none';
  else $('authDialog_login').style.display = 'block';
  $('authDialog_logout').style.display = 'none';
}

//функция отображения диалога в режиме "логоут"
function authDialog_logout_show() {
  $('authDialog_login').style.display = 'none';
  if ($('authDialog_logout').style.display == 'block') $('authDialog_logout').style.display = 'none';
  else $('authDialog_logout').style.display = 'block';
}

//нажатие кнопки "логин"
function authDialog_login() {
}

//нажатие кнопки "отмена"
function authDialog_cancel() {
  $('authDialog_login').style.display = 'none';
  $('authDialog_logout').style.display = 'none';
}

//нажатие кнопки "логоут"
function authDialog_logout() {
}

//всплывающие кнопки для редактирования элементов меню
function menuCADialog_show() {
  $('menuCADialog').style.visibility = 'visible';
  $('menuCADialog').style.left = this.getLeft()+this.getWidth()-1+'px';
  $('menuCADialog').style.top = this.getTop()+'px';
  $('menuCADialog').menuItem = this.parentNode;
}

function menuCADialog_hide() {
  $('menuCADialog').style.visibility = 'hidden';
}

function menuCADialog_delete() {
  showYesNo('Вы действительно хотите удалить этот пункт?', function() {
    window.setTimeout("window.clearTimeout(closeMenuTimer)",timeToClose / 2);
    jsonR.onSuccess = function(json) {
      $('menuCADialog').menuItem.parentNode.removeChild($('menuCADialog').menuItem);
    };
    if ($('menuCADialog').menuItem.className=="mi") 
    jsonR.send('remFile='+$('menuCADialog').menuItem.getAttribute('menu.id'));
    else jsonR.send('remFolder='+$('menuCADialog').menuItem.getAttribute('menu.id'));
  });
}

function chgFileSubmitter(m, f, g, menuid,e) {
  jsonR.onSuccess = function(json) {
    $('menuCADialog').menuItem.setAttribute('menu.group',g);
    $('menuCADialog').menuItem.setAttribute('menu.name',m);
    $('menuCADialog').menuItem.setAttribute('menu.fname',f);
    var as = $('menuCADialog').menuItem.getElementsByTagName('a');
    $(as[0]).set('text',m);
    Message(json.message);
  };
  jsonR.send('chgFile='+menuid+'&m='+m+'&f='+f+'&g='+g);
}

function chgMenuSubmitter(m, f, g, menuid,e) {
  jsonR.onSuccess = function(json) {
    $('menuCADialog').menuItem.setAttribute('menu.group',g);
    $('menuCADialog').menuItem.setAttribute('menu.name',m);
    $('menuCADialog').menuItem.setAttribute('menu.fname',f);
    var as = $('menuCADialog').menuItem.getElementsByTagName('a');
    $(as[0]).set('text',m);
    Message(json.message);
  };
  jsonR.send('chgFolder='+menuid+'&m='+m+'&f='+f+'&g='+g);
}

function menuCADialog_change() {
  window.setTimeout("window.clearTimeout(closeMenuTimer)",timeToClose / 2);
  if ($('menuCADialog').menuItem.className=="mi") 
  submitter = chgFileSubmitter;
  else submitter = chgMenuSubmitter;

  menuElementDialog(
    $('menuCADialog').menuItem.getAttribute('menu.name'),
    $('menuCADialog').menuItem.getAttribute('menu.fname'),
    $('menuCADialog').menuItem.getAttribute('menu.group'), 
    submitter, 
    parseInt($('menuCADialog').menuItem.getAttribute('menu.id')), 
    $('menuCADialog').menuItem);
}


function menuElementDrag(e) {
/*  alert(e.parentNode.uid);
  alert(e.parentNode.menuItem);*/
}

