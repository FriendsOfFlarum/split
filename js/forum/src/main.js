import { extend } from 'flarum/extend';
import Model from 'flarum/Model';

import addSplitControl from 'flagrow/split/addSplitControl';
import SplitController from 'flagrow/split/components/SplitController';
import DiscussionSplit from 'flagrow/split/components/DiscussionSplit';

app.initializers.add('flagrow-split', app => {

    app.store.models.discussions.prototype.canSplit = Model.attribute('canSplit');

    app.postComponents.discussionSplit = DiscussionSplit;

    var splitController = new SplitController();

    addSplitControl(splitController);
});
