if (! window.TMTI) { window.TMTI = {}; }

TMTI.Uploader = function(options)
{
    this.options = $.extend({
        formSelector: '',
        fileFieldSelector: ''
    }, options);

    this.observer = new TMTI.Observer();

    $($.proxy(this.onDomReady, this));
};

$.extend(TMTI.Uploader.prototype,
{
    onDomReady: function()
    {
        this.form = $(this.options.formSelector);
        this.fileField = this.form.find(this.options.fileFieldSelector);

        this.form.ajaxForm({
            iframe: true,
            dataType: 'json',
            success: $.proxy(this.onSuccess, this)
        });

        this.fileField.change($.proxy(this.onFileChosen, this));
    },

    onFileChosen: function() {
        if (this.fileField.val().length) {
            this.form.submit();
        }
    },

    onSuccess: function(data)
    {
        if (data.success) {
            this.observer.triggerCallbacks(data.image);
            this.form.resetForm();
        }
    },

    addListener: function(cb)
    {
        this.observer.addListener(cb)
    }
});