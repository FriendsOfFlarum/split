import Extend from 'flarum/common/extenders';
import Discussion from 'flarum/common/models/Discussion';
import DiscussionSplit from './components/DiscussionSplit';

export default [
    new Extend.Model(Discussion) //
        .attribute<boolean>('canSplit'),

    new Extend.PostTypes() //
        .add('discussionSplit', DiscussionSplit),
];
