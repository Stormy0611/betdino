import Effect from "./Effect";

export default class RakebackButtonEffect extends Effect {

    create(insertAfter, color, width = 142, height = 41, length = .9) {
        if(!insertAfter) return;

        this.numStars = 25;

        const init = () => {
            this.stars = [];
            for (let i = 0; i < this.numStars; i++) this.stars.push({
                x: Math.random() * a.canvas.width,
                y: Math.random() * a.canvas.height,
                z: Math.random() * a.canvas.width,
                o: "0." + Math.floor(99 * Math.random()) + 1,
                opacity: 1
            });
        }

        const a = this.createCanvas(insertAfter, '2d', true, width, height);

        init();

        this.focalLength = length * a.canvas.width;
        this.warp = 0; this.centerX = a.canvas.width / 2;
        this.centerY = a.canvas.height / 2;
        this.radius = "0." + Math.floor(6 * Math.random()) + 1;
        this.speed = .1;

        const move = () => {
            for (let i = 0; i < this.numStars; i++) {
                let t = this.stars[i];
                t.y += this.speed;
                if(t.y >= a.canvas.height) {
                    t.x = Math.random() * a.canvas.width;
                    t.y = 0;
                }
            }
        }

        const draw = () => {
            let centerX = a.canvas.width / 2;
            let centerY = a.canvas.height / 2;

            a.context.clearRect(0, 0, a.canvas.width, a.canvas.height);

            a.context.fillStyle = "rgba(" + color + ", " + this.radius + ")";
            for (let i = 0; i < this.numStars; i++) {
                let m = this.stars[i];
                let t = (m.x - centerX) * (this.focalLength / m.z);
                t += centerX;

                let e = (m.y - centerY) * (this.focalLength / m.z);
                e += centerY;
                let o = this.focalLength / m.z;

                a.context.globalAlpha = m.opacity;
                a.context.beginPath();
                a.context.arc(t, e, o, 0, 2 * Math.PI, !1);
                a.context.fillStyle = "rgba(" + color + ", " + m.o + ")";
                a.context.fill();
                a.context.globalAlpha = 1;
            }

            move();
            requestAnimationFrame(draw);
        }

        requestAnimationFrame(draw);
    }

}
