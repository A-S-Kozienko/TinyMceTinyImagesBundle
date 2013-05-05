
if (! window.TMTI) { window.TMTI = {}; }

TMTI.List = function(options)
{
    this.options = $.extend({
        containerSelector: '',
        imageSelector: '',
        removeSelector: '',
        insertSelector: '',
        listUrl: '',
        removeUrl: '',
        imageTemplate: ''
    }, options);

    this.images = {};
    this.observer = new TMTI.Observer();

    $($.proxy(this.onDomReady, this));
};

$.extend(TMTI.List.prototype,
{
    onDomReady: function()
    {
        this.container = $(this.options.containerSelector);
        if (! this.container.length) {
            throw new Error('Could not find container');
        }

        this.container.on('click', this.options.removeSelector, $.proxy(this.onRemove, this));
        this.container.on('dblclick', this.options.insertSelector, $.proxy(this.onInsert, this));

        this.loadList();
    },

    loadList: function() {
        var self = this;
        $.ajax({
            url: this.options.listUrl,
            success: function(data) {
                if (data.success) {
                    $.each(data.images, function(index, image) {
                        self.add(image)
                    });
                }
            }
        });
    },

    add: function(data)
    {
        var view = $(Mustache.render(this.options.imageTemplate, data));
        view.data('data', data);

        this.images[data.id] = view;
        this.container.prepend(view);
    },

    remove: function(id)
    {
        if (this.images[id]) {
            this.images[id].remove();
            delete this.images[id];
        }
    },

    onInsert: function(event)
    {
        var data = $(event.target).parents(this.options.imageSelector).data('data');
        this.observer.triggerCallbacks(data);
    },

    onRemove: function(event)
    {
        if (confirm('Are you sure?')) {
            var data = $(event.target).parents(this.options.imageSelector).data('data');

            $.ajax({
                url: this.options.removeUrl.replace('image-id', data.id)
            });

            this.remove(data.id);
        }
    },

    addListener: function(cb)
    {
        this.observer.addListener(cb);
    }
});