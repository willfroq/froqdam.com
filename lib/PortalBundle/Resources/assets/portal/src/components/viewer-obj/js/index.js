import { UiComponent } from "../../ui-component/js";
import * as THREE from "three";
import { OrbitControls } from "three/examples/jsm/controls/OrbitControls.js";
import { OBJLoader } from "three/examples/jsm/loaders/OBJLoader.js";

class ViewerOBJ extends UiComponent {
    constructor() {
        super();

        this.url = null;
    }

    connectedCallback() {
        super.connectedCallback();

        setTimeout(this.render.bind(this));
    }

    initialize() {
        const canvas = document.querySelector("canvas#webgl");
        const scene = new THREE.Scene();
        const objLoader = loadOBJModel(this.url, scene);
        const gridHelper = createGrid(scene);
        const { ambientLight, directionalLight } = createLights(scene);
        const sizes = setupSizes();
        const camera = setupCamera(sizes, scene);
        const controls = createControls(camera, canvas);
        const renderer = setupRenderer(canvas, sizes);
        animate(scene, camera, controls, renderer);

        window.addEventListener("resize", () => {
            handleWindowResize(camera, renderer);
        });
    }

    render() {
        this.url = this.getAttribute("url");
        this.initialize();
    }
}

function loadOBJModel(url, scene) {
    const objLoader = new OBJLoader();

    document.dispatchEvent(new Event('showAssetPreviewLoader'));
    objLoader.load(
        url,
        (obj) => {
            obj.scale.set(0.75, 0.75, 0.75);
            obj.position.set(0, 0.75, 0);
            scene.add(obj);
            document.dispatchEvent(new Event('hideAssetPreviewLoader'));
        },
        () => { return false; },
        () => {
            document.dispatchEvent(new Event('hideAssetPreviewLoader'));
        }
    );

    return objLoader;
}

function createGrid(scene) {
    const size = 10;
    const divisions = 10;
    const gridHelper = new THREE.GridHelper(size, divisions);
    gridHelper.material.color.set(0xffff00);
    scene.add(gridHelper);
    return gridHelper;
}

function createLights(scene) {
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

    return {
        ambientLight,
        directionalLight,
    };
}

function setupSizes() {
    return {
        width: window.innerWidth,
        height: window.innerHeight,
    };
}

function setupCamera(sizes, scene) {
    const camera = new THREE.PerspectiveCamera(
        75,
        sizes.width / sizes.height,
        0.1,
        100
    );
    camera.position.set(1, 0.5, 2);
    scene.add(camera);
    return camera;
}

function createControls(camera, canvas) {
    const controls = new OrbitControls(camera, canvas);
    controls.target.set(0, 0.75, 0);
    controls.enableDamping = true;
    return controls;
}

function setupRenderer(canvas, sizes) {
    const renderer = new THREE.WebGLRenderer({
        canvas: canvas,
    });
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.setSize(sizes.width, sizes.height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0xffffff, 0);
    return renderer;
}

function animate(scene, camera, controls, renderer) {
    const clock = new THREE.Clock();
    let previousTime = 0;

    function tick() {
        const elapsedTime = clock.getElapsedTime();
        const deltaTime = elapsedTime - previousTime;
        previousTime = elapsedTime;

        controls.update();
        renderer.render(scene, camera);
        window.requestAnimationFrame(tick);
    }

    tick();
}

function handleWindowResize(camera, renderer) {
    sizes.width = window.innerWidth;
    sizes.height = window.innerHeight;

    camera.aspect = sizes.width / sizes.height;
    camera.updateProjectionMatrix();

    renderer.setSize(sizes.width, sizes.height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
}

customElements.define("app-viewer-obj", ViewerOBJ);
