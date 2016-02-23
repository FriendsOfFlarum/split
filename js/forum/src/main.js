import { extend } from 'flarum/extend';
import Model from 'flarum/Model';
import Discussion from 'flarum/models/Discussion';

import addSplitControl from 'flagrow/split/addSplitControl';

import SplitController from 'flagrow/split/components/SplitController';

//import extendDiscussionPage from 'flagrow/split/extendDiscussionPage';

app.initializers.add('flagrow-split', app => {

    app.store.models.discussions.prototype.canSplit = Model.attribute('canSplit');

    //extendDiscussionPage();

    var splitController = new SplitController();
    splitController.log();

    addSplitControl(splitController);
});
