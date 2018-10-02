import { extend } from 'flarum/extend';
import Model from 'flarum/Model';

import addSplitControl from './addSplitControl';
import SplitController from './components/SplitController';
import DiscussionSplit from './components/DiscussionSplit';

app.initializers.add('flagrow-split', app => {

    app.store.models.discussions.prototype.canSplit = Model.attribute('canSplit');

    app.postComponents.discussionSplit = DiscussionSplit;

    var splitController = new SplitController();

    addSplitControl(splitController);
});
