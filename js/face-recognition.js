/**
 * Face Recognition Module
 * Handles comparison of face descriptors for authentication
 */

/**
 * Calculate the distance between two face descriptors
 * Lower distance indicates higher similarity (better match)
 * Threshold of 0.6 is recommended by face-api.js
 * 
 * @param {Float32Array} descriptor1 - First face descriptor
 * @param {Float32Array} descriptor2 - Second face descriptor
 * @returns {number} - Distance between descriptors
 */
function calculateFaceDescriptorDistance(descriptor1, descriptor2) {
    if (!(descriptor1 instanceof Float32Array)) {
        descriptor1 = new Float32Array(descriptor1);
    }
    
    if (!(descriptor2 instanceof Float32Array)) {
        descriptor2 = new Float32Array(descriptor2);
    }
    
    return faceapi.euclideanDistance(descriptor1, descriptor2);
}

/**
 * Compare a face descriptor to a database of known descriptors
 * Returns the best match if the distance is below threshold
 * 
 * @param {Float32Array} queryDescriptor - Face descriptor to search for
 * @param {Array} knownDescriptors - Array of known face descriptors with user ids
 * @param {number} threshold - Maximum distance threshold (default: 0.6)
 * @returns {Object|null} - Best match info or null if no match found
 */
function findBestMatch(queryDescriptor, knownDescriptors, threshold = 0.6) {
    if (!knownDescriptors || knownDescriptors.length === 0) {
        console.log('No known descriptors provided');
        return null;
    }
    
    let bestMatch = null;
    let bestDistance = Infinity;
    
    // Convert to Float32Array if needed
    if (!(queryDescriptor instanceof Float32Array)) {
        queryDescriptor = new Float32Array(queryDescriptor);
    }
    
    // Find best match by comparing distances
    knownDescriptors.forEach(known => {
        const distance = calculateFaceDescriptorDistance(
            queryDescriptor, 
            known.descriptor
        );
        
        if (distance < bestDistance) {
            bestDistance = distance;
            bestMatch = {
                userId: known.userId,
                username: known.username,
                distance: distance
            };
        }
    });
    
    // Return best match if below threshold
    if (bestMatch && bestMatch.distance <= threshold) {
        return bestMatch;
    }
    
    return null;
}

/**
 * Real-time face recognition from video stream
 * Continuously detects and recognizes faces
 * 
 * @param {HTMLVideoElement} videoElement - Video element with camera stream
 * @param {Array} knownDescriptors - Array of known face descriptors
 * @param {Function} onRecognized - Callback when face is recognized
 * @param {Function} onUnrecognized - Callback when face is detected but not recognized
 * @param {Function} onNoFace - Callback when no face is detected
 */
async function startFaceRecognition(
    videoElement, 
    knownDescriptors, 
    onRecognized, 
    onUnrecognized, 
    onNoFace
) {
    // Create canvas for visualization
    const canvas = document.createElement('canvas');
    videoElement.parentNode.appendChild(canvas);
    canvas.style.position = 'absolute';
    canvas.style.top = '0';
    canvas.style.left = '0';
    
    async function recognize() {
        // Detect face in current video frame
        const detection = await detectFace(videoElement);
        
        if (detection) {
            // Draw detection on canvas
            drawFaceDetection(videoElement, canvas, detection);
            
            // Compare with known faces
            const match = findBestMatch(detection.descriptor, knownDescriptors);
            
            if (match) {
                onRecognized && onRecognized(match);
            } else {
                onUnrecognized && onUnrecognized();
            }
        } else {
            // Clear canvas if no face detected
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            onNoFace && onNoFace();
        }
        
        // Continue recognition loop
        requestAnimationFrame(recognize);
    }
    
    // Start recognition loop
    recognize();
}