
if (! window.TMTI) { window.TMTI = {}; }

TMTI.Observer = function() {
    this.pubsub = { c:[], f:[] };
};

$.extend(TMTI.Observer.prototype,
{
    addListener: function(f, c)
    {
        var _c = c || null;
        var index = $.inArray(f, this.pubsub.f);
        if(index == -1) {
            this.pubsub.c.push(_c);
            this.pubsub.f.push(f);
        }
    },

    removeListener: function(f)
    {
        var index = $.inArray(f, this.pubsub.f);
        if(index >= 0) {
            this.pubsub.c.splice(index, 1);
            this.pubsub.f.splice(index, 1);
        }
    },

    removeAllListeners: function()
    {
        this.pubsub.c = [];
        this.pubsub.f = [];
    },

    triggerCallbacks: function()
    {
        for(var i in this.pubsub.f) {
            var f = this.pubsub.f[i];
            var c = this.pubsub.c[i] || window;
            f.apply(c, arguments);
        }
    }
});
