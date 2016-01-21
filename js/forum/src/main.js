import { extend } from 'flarum/extend';
import Model from 'flarum/Model';
import Discussion from 'flarum/models/Discussion';

import addSplitControl from 'flagrow/split/addSplitControl';
// import SplitController from 'flagrow/split/utils/SplitController'

app.initializers.add('flagrow-split', app => {
    app.store.models.discussions.prototype.canSplit = Model.attribute('canSplit');

    Object.assign(Discussion.prototype, {
        splitting: m.prop(false)
    });

    addSplitControl();
});
