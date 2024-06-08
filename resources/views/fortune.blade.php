<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FORTUNE</title>
    <link rel="stylesheet" href="{{ asset('fort/css/reset.css') }}" />
    <link rel="stylesheet" href="{{ asset('fort/css/main.css') }}" />
</head>
<body>
<div 	id="app" :class="{'is-spun': isSpun}"
        :style="{'--products-amount': productsAmountVisible}">
    <audio src="{{ asset('fort/audio/onion-capers-by-kevin-macleod-from-filmmusic-io.mp3') }}" ref="audio"></audio>
    <div class="product_wrap">
        <form method="post" class="product" :style="{'--product-shift-by': -(this.randomShift * productsAmountVisible) + '%'}" action="{{ route('fortune.catch') }}">
            @csrf
            <img :src="`{{ asset('fort/images/products/${product.id}.jpg') }}`" alt="">
            <div class="product_info">
                <h2 v-text="product.name"></h2>
                <input type="hidden" name="id_product" value="{{ $winFortune['id'] }}">
                <button type="submit" class="btn is-primary" name="claim_gift_submit">Забрати подарунок</button>
            </div>
        </form>
    </div>
    <div class="products_arrow_title">Ваш приз:</div>
    <div class="products_wrap">
        <div class="products_arrow_line"></div>
        <ul class="products" :style="spinCss">
            <li
                class="products_item"
                v-for="product in products"
                :key="product.id"
            >
                <img :src="`{{ asset('fort/images/products/${product.id}.jpg') }}`" />
            </li>
        </ul>
    </div>
    <button @click="spin" class="btn is-primary btn_spin">Крутити</button>
</div>
<script src="fort/js/vue.js"></script>
<script>
    function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    function getRandomFloat(min, max) {
        return parseFloat((Math.random() * (max - min) + min).toFixed(2));
    }
    function shuffle(a) {
        var j, x, i;
        for (i = a.length - 1; i > 0; i--) {
            j = Math.floor(Math.random() * (i + 1));
            x = a[i];
            a.splice(i, 1, a[j]);
            a.splice(j, 1, x);
        }
        return a;
    }
    new Vue({
        el: '#app',
        data: {
            productsAmountVisible: 5, // should be an odd number
            productPlaceMin: null,
            productPlaceMax: null,
            productRandomIndex: null,
            products: <?php echo $jsonFortune; ?>,
            product: null,
            productsSlideBy: 0,
            randomShiftDelta: 2.1,
            randomShift: 0,
            spinAnimationDuration: getRandomFloat(10, 20),
            isSpun: false,
        },
        computed: {
            productsCount() {
                return this.products.length;
            },
            spinCss() {
                return {
                    '--product-slide-by': -this.productsSlideBy + '%',
                    '--product-slide-animation-duration': this.spinAnimationDuration + 's',
                };
            },
        },
        created() {
            this.product = this.products[0];
            this.productPlaceMin = this.productsCount / 2;
            this.productPlaceMax = this.productsCount - Math.ceil(this.productsAmountVisible / 2);
            this.productRandomIndex = getRandomInt(this.productPlaceMin, this.productPlaceMax);

            shuffle(this.products);

            const productToSwap = this.products[this.productRandomIndex - 1];
            const productIndex = this.products.indexOf(this.product);

            this.products.splice(this.productRandomIndex - 1, 1, this.product);
            this.products.splice(productIndex, 1, productToSwap);
        },
        methods: {
            spin() {
                this.playAudio();
                const productWidthPercent = 100 / this.productsAmountVisible;
                this.randomShift = getRandomFloat(-(productWidthPercent / this.randomShiftDelta), (productWidthPercent / this.randomShiftDelta));
                this.productsSlideBy =  ( this.productRandomIndex - (this.productsCount - this.productPlaceMax) )
                    * productWidthPercent + this.randomShift;
                setTimeout(() => {
                    this.isSpun = true;
                }, this.spinAnimationDuration * 1000);
            },
            playAudio() {
                const fadePoint = this.spinAnimationDuration / 3;
                const audio = this.$refs.audio;
                audio.currentTime = 1;
                audio.play();

                const volumeStep = 100 / (this.spinAnimationDuration - this.spinAnimationDuration / 3 - 3) / 1000;

                let fadeAudio = setInterval(() => {
                    if ((audio.currentTime >= fadePoint) && (audio.volume > 0)) {
                        if(audio.volume - volumeStep > 0) {
                            audio.volume -= volumeStep;
                        }
                        else {
                            audio.volume = 0;
                        }
                    }
                    if (audio.volume <= 0) {
                        clearInterval(fadeAudio);
                    }
                }, 100);
            },
        },
    });
</script>
</body>
</html>
