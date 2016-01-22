import { extend } from 'flarum/extend';
import Model from 'flarum/Model';
import Discussion from 'flarum/models/Discussion';

import addSplitControl from 'flagrow/split/addSplitControl';

import extendDiscussionPage from 'flagrow/split/extendDiscussionPage';

app.initializers.add('flagrow-split', app => {

    app.store.models.discussions.prototype.canSplit = Model.attribute('canSplit');

    extendDiscussionPage();

    addSplitControl();
});
