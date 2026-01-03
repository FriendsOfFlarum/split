import app from 'flarum/forum/app';

import addSplitControl from './addSplitControl';
import SplitController from './SplitController';

app.initializers.add('fof-split', () => {
  window.app.__fof_split = {
    splitting: false,
    showSplitTos: {},
    splitController: null,
  };

  window.app.__fof_split.splitController = new SplitController();

  addSplitControl();
});
