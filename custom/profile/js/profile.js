jQuery(function($){
	$.datepicker.regional['ru'] = {
		closeText: 'Закрыть',
		prevText: '&#x3c;Пред',
		nextText: 'След&#x3e;',
		currentText: 'Сегодня',
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
		'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
		'Июл','Авг','Сен','Окт','Ноя','Дек'],
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		weekHeader: 'Нед',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
});

CMS.ui.attach.edit_user_form_profile = function(){
    $("input.input-date").datepicker({
        dateFormat:'dd.mm.yy'
    });
    var input = $('#profile-picture');
    input.change(function(){
        var el = $(this);
        var val = $(this).val();
        var bar = $('.upload-bar');
        var picture_area = $('.profile-picture');
        if(!val)
            return;
        bar.empty().html('<div class="progress"><div class="bar"></div></div>');
        bar = $('.upload-bar .bar');
        $.upload5(CMS.settings.web_root + 'profile/file_upload',this,function(data,code){
            if(code != 200){
                CMS.ui.modal('Не удалось загрузить файл');
                return;
            }
            data = JSON.parse(data);
            if(!data.status){
                CMS.ui.modal('Загрузка файла не удалась. Попробуйте еще раз.');
                return;
            }
            if(data.fmessage)
                CMS.ui.modal(data.fmessage);
            if(data.message)
                CMS.ui.modal(Base64.decode(data.message));
            picture_area.html(data.picture);
            el.val('');
        },function(percent,bu,bt){
           bar.css('width',percent + '%');
           bu = parseInt(bu/1024);
           bt = parseInt(bt/1024);
           bar.text(bu + 'кб / ' + bt + 'кб');
        });
    });
};