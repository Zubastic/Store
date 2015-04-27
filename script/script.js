// Интерфейс для рендеров.
function Renderer() {
    this.Render = function(){ };
    this.Derender = function(){ };
}

// Класс модального окна.
function ModalWnd(renderer) {
    var _renderer = renderer;

    this.Show = function() {
        $('.fader').show();
        _renderer.Render();
    };
    
    this.Hide = function() {
        $('.fader').hide();
        _renderer.Derender();
    };
    
    // Подписки.
    $('.fader').click(function() {
        Hide();
    });
    
    // TODO: Исправить (Рисовальщик рисует, а не все подряд делает).
    _renderer.CloseRequested = function() {
        Hide();
    };
    
    return this;
}

// Класс рисовальщика для зума картинки.
function ImgZoomRenderer(imgPath) {
    prototype = Renderer();

    var _img = imgPath;
    
    this.CloseRequested = function(){ };
    
    this.Render = function() {
        $('.ImgZoomModal').show();
        $('.ImgZoomed').attr('src', _img);
    };
    
    this.Derender = function() {
        $('.ImgZoomModal').hide();
    };
    
    // TODO: Исправить (Рисовальщик рисует, а не все подряд делает).
    $('.ImgZoomModal').click(function () {
        CloseRequested();
    });
    
    return this;
}

// Класс рисовальщика для логина.
function LoginWndRenderer() {
    prototype = Renderer();

    this.CloseRequested = function(){ };
    
    this.Render = function() {
        $('.UserLoginModalWnd').show();
        //$('.ImgZoomed').attr('src', _img);
    };
    
    this.Derender = function() {
        $('.UserLoginModalWnd').hide();
    };
    
    // TODO: Закрытие (CloseRequested).
    
    return this;
}

$(document).ready(function() {
    
    $('.ItemImg').click(function(obj) {
        var img = $(obj.target).attr('src');
        var renderer = ImgZoomRenderer(img);
        var wnd = ModalWnd(renderer);
        wnd.Show();
    });
    
    
});


function Login() {
    var renderer = LoginWndRenderer();
    var wnd = ModalWnd(renderer);
    wnd.Show();
};

// TODO: Сделать клонирование.
function addContact() {
    var element = document.createElement('div');
    var h = '<select><option value="Email">Email</option><option value="Phone">Phone</option></select>';
    h += '<input type="text" name ="contact[]" value=""></input>';
    element.innerHTML = h;
    $('.userInfo').append(element);
}

function removeContact(ind) {
    //var element = document.createElement('div');
    var s = '.cont';
    s += ind;
    
    $(s).remove();
}