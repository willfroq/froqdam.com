import { UiComponent } from "../../ui-component/js";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls.js";
import { GLTFLoader } from "three/examples/jsm/loaders/GLTFLoader.js";

class ViewerGLB extends UiComponent {
    constructor() {
        super();

        this.url = null;
    }

    connectedCallback() {
        super.connectedCallback();

        setTimeout(this.render.bind(this));
    }

    initialize() {
        /**
         * Base
         */

        // Canvas
        const canvas = document.querySelector("canvas#webgl");

        // Scene
        const scene = new THREE.Scene();

        /**
         * Models
         */
        const gltfLoader = new GLTFLoader();

        document.dispatchEvent(new Event('showAssetPreviewLoader'));
        gltfLoader.load(
            this.url,
            (gltf) => {
                gltf.scene.scale.set(0.01, 0.01, 0.01);
                scene.add(gltf.scene);
                document.dispatchEvent(new Event('hideAssetPreviewLoader'));
            },
            () => { return false; },
            () => {
                document.dispatchEvent(new Event('hideAssetPreviewLoader'));
            }
        );

        /**
         * Grid
         */
        const size = 10;
        const divisions = 10;

        const gridHelper = new THREE.GridHelper(size, divisions);
        gridHelper.material.color.set(0xffff00);
        scene.add(gridHelper);

        /**
         * Lights
         */
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
        scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.6);
        directionalLight.castShadow = true;
        directionalLight.shadow.mapSize.set(1024, 1024);
        directionalLight.shadow.camera.far = 15;
        directionalLight.shadow.camera.left = -7;
        directionalLight.shadow.camera.top = 7;
        directionalLight.shadow.camera.right = 7;
        directionalLight.shadow.camera.bottom = -7;
        directionalLight.position.set(-5, 5, 0);
        scene.add(directionalLight);

        /**
         * Sizes
         */
        const sizes = {
            width: window.innerWidth,
            height: window.innerHeight,
        };

        window.addEventListener("resize", () => {
            // Update sizes
            sizes.width = window.innerWidth;
            sizes.height = window.innerHeight;

            // Update camera
            camera.aspect = sizes.width / sizes.height;
            camera.updateProjectionMatrix();

            // Update renderer
            renderer.setSize(sizes.width, sizes.height);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        });

        /**
         * Camera
         */
        // Base camera
        const camera = new THREE.PerspectiveCamera(
            75,
            sizes.width / sizes.height,
            0.1,
            100
        );
        camera.position.set(1, 0.5, 2);
        scene.add(camera);

        // Controls
        const controls = new OrbitControls(camera, canvas);
        controls.target.set(0, 0.75, 0);
        controls.enableDamping = true;

        /**
         * Renderer
         */
        const renderer = new THREE.WebGLRenderer({
            canvas: canvas,
        });
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.setSize(sizes.width, sizes.height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setClearColor(0xffffff, 0);

        /**
         * Animate
         */
        const clock = new THREE.Clock();
        let previousTime = 0;

        const tick = () => {
            const elapsedTime = clock.getElapsedTime();
            const deltaTime = elapsedTime - previousTime;
            previousTime = elapsedTime;

            // Update controls
            controls.update();

            // Render
            renderer.render(scene, camera);

            // Call tick again on the next frame
            window.requestAnimationFrame(tick);
        };

        tick();
    }

    render() {
        /**
         * Initialize
         */

        this.url = this.getAttribute("url");
        this.initialize();
    }
}

customElements.define("app-viewer-glb", ViewerGLB);
