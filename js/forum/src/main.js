import { extend } from 'flarum/extend';
import Model from 'flarum/Model';

import addSplitControl from 'flagrow/split/addSplitControl';

app.initializers.add('flagrow-split', app => {
    app.store.models.posts.prototype.canSplit = Model.attribute('canSplit');

    addSplitControl();
});
