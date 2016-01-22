export default class SplitController {
    init() {
        console.log('SplitController init');
        this.splitting = false;
    }

    isSplitting() {
        console.log('checked splitting, result:' + this.splitting);
        return this.splitting;
    }

    startSplitting() {
        console.log('Started splitting');
        this.splitting = true;
        m.redraw.strategy("diff");
        m.redraw();
    }

    endSplitting() {
        console.log('Ended splitting');
        this.splitting = false;
    }
}
