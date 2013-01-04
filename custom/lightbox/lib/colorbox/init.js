CMS.ui.attach.colorbox = function(){
    var opt = {
        current: 'Изображение {current} из {total}',
        previous: 'Назад',
        next: 'Вперед',
        close: 'Закрыть',
        xhrError: 'Ошибка загрузки',
        imgError: 'Не удалось загрузить изображение',
        /***/
        maxWidth: '90%'
    }
    $(CMS.settings.lightbox.selector).colorbox(opt);
}