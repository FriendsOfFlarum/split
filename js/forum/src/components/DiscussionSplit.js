import EventPost from "flarum/components/EventPost";

export default class DiscussionSplit extends EventPost {
    static initProps(props) {
        super.initProps(props);

        console.log(props);
    }

    icon() {
        return 'map-o';
    }

}
