/**
 * Face Detection Module
 * Uses face-api.js (built on TensorFlow.js) to detect and recognize faces
 */

// Path to pre-trained models
const MODEL_URL = './models';

/**
 * Initialize face detection by loading all required models
 * @returns {Promise} Resolves when all models are loaded
 */
async function initFaceDetection() {
    try {
        // Check if faceapi is available
        if (typeof faceapi === 'undefined') {
            throw new Error("face-api.js library not loaded properly");
        }
        
        console.log('Loading face detection models from:', MODEL_URL);
        await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        console.log('Face detection models loaded successfully');
        return true;
    } catch (error) {
        console.error('Error loading face detection models:', error);
        throw error;
    }
}

/**
 * Detect a face in an image/video element and extract its descriptor
 * @param {HTMLElement} mediaElement - Image or video element
 * @returns {Object|null} - Detection result or null if no face detected
 */
async function detectFace(mediaElement) {
    try {
        // Get detections from image/video
        const detections = await faceapi.detectSingleFace(mediaElement)
            .withFaceLandmarks()
            .withFaceDescriptor();
        
        if (!detections) {
            console.log('No face detected');
            return null;
        }
        
        return {
            detection: detections.detection,
            landmarks: detections.landmarks,
            descriptor: detections.descriptor,
            boundingBox: detections.detection.box
        };
    } catch (error) {
        console.error('Error detecting face:', error);
        throw error;
    }
}

/**
 * Draw facial landmarks and detection box on a canvas
 * @param {HTMLElement} mediaElement - Source image/video element
 * @param {HTMLCanvasElement} canvas - Canvas to draw on
 * @param {Object} detection - Face detection result
 */
function drawFaceDetection(mediaElement, canvas, detection) {
    if (!detection) return;
    
    const displaySize = { 
        width: mediaElement.width || mediaElement.videoWidth, 
        height: mediaElement.height || mediaElement.videoHeight 
    };
    
    // Resize canvas to match media element
    faceapi.matchDimensions(canvas, displaySize);
    
    // Draw the detection results
    const resizedDetection = faceapi.resizeResults(detection, displaySize);
    
    // Clear canvas
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Draw detection box
    faceapi.draw.drawDetections(canvas, resizedDetection);
    
    // Draw landmarks
    if (resizedDetection.landmarks) {
        faceapi.draw.drawFaceLandmarks(canvas, resizedDetection.landmarks);
    }
}