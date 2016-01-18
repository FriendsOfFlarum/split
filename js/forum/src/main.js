import { extend } from 'flarum/extend';

import addSplitControl from 'flagrow/split/addSplitControl';

app.initializers.add('flagrow-split', app => {

    addFlagControl();
});
