<?php

namespace App\Services\AI;

use App\Models\KycVerification;
use App\Models\KycDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class KycVerificationService
{
    /**
     * Cliente HTTP para realizar solicitudes a APIs externas
     */
    protected $client;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
    }
    
    /**
     * Procesar una verificación KYC utilizando IA
     *
     * @param KycVerification $verification
     * @return array
     */
    public function processVerification(KycVerification $verification)
    {
        try {
            $results = [
                'document_verification' => $this->verifyDocuments($verification),
                'face_verification' => $this->verifyFaceMatch($verification),
                'liveness_detection' => $this->detectLiveness($verification),
                'risk_assessment' => $this->assessRisk($verification),
                'processed_at' => now(),
                'verification_score' => 0,
                'recommendation' => '',
                'flags' => []
            ];
            
            // Calcular puntuación general y recomendación
            $results = $this->calculateOverallScore($results);
            
            // Guardar resultados en el documento KYC
            $this->saveResults($verification, $results);
            
            return $results;
        } catch (\Exception $e) {
            Log::error('Error en la verificación KYC con IA: ' . $e->getMessage(), [
                'verification_id' => $verification->id,
                'user_id' => $verification->user_id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'Error en el procesamiento de la verificación: ' . $e->getMessage(),
                'processed_at' => now()
            ];
        }
    }
    
    /**
     * Verificar la autenticidad de los documentos
     *
     * @param KycVerification $verification
     * @return array
     */
    protected function verifyDocuments(KycVerification $verification)
    {
        $results = [
            'success' => true,
            'score' => 0,
            'details' => [],
            'flags' => []
        ];
        
        try {
            // Verificar el frente del documento
            if (!$verification->id_front_path || !Storage::disk('private')->exists($verification->id_front_path)) {
                throw new \Exception('Imagen frontal del documento no encontrada');
            }
            
            $frontResults = $this->analyzeDocument(
                Storage::disk('private')->path($verification->id_front_path),
                $verification->id_type
            );
            
            // Inicializar resultados del reverso
            $backResults = [
                'success' => true,
                'score' => 0,
                'details' => [],
                'flags' => []
            ];
            
            // Verificar el reverso del documento si existe
            if ($verification->id_back_path && Storage::disk('private')->exists($verification->id_back_path)) {
                $backResults = $this->analyzeDocument(
                    Storage::disk('private')->path($verification->id_back_path),
                    $verification->id_type
                );
            }
            
            // Verificar la información extraída
            $dataResults = $this->verifyExtractedData($frontResults, $backResults, $verification);
            
            // Combinar resultados
            $results['details'] = [
                'front_document' => $frontResults,
                'back_document' => $backResults,
                'data_verification' => $dataResults
            ];
            
            // Calcular puntuación
            $results['score'] = $this->calculateDocumentScore($frontResults, $backResults, $dataResults);
            
            // Identificar banderas
            $results['flags'] = array_merge(
                $frontResults['flags'] ?? [],
                $backResults['flags'] ?? [],
                $dataResults['flags'] ?? []
            );
            
        } catch (\Exception $e) {
            Log::error('Error en la verificación de documentos: ' . $e->getMessage());
            $results['success'] = false;
            $results['error'] = $e->getMessage();
        }
        
        return $results;
    }
    
    /**
     * Analizar un documento utilizando IA
     *
     * @param string $documentPath
     * @param string $documentType
     * @return array
     */
    protected function analyzeDocument($documentPath, $documentType)
    {
        // En un entorno real, aquí se conectaría con un servicio de IA para análisis de documentos
        // Como Microsoft Azure Document Intelligence, Google Cloud Vision, o AWS Textract
        
        // Simulación de resultados para desarrollo
        $results = [
            'is_authentic' => true,
            'confidence' => rand(85, 99) / 100,
            'extracted_data' => [
                'document_number' => Str::random(10),
                'full_name' => 'اسم تجريبي',
                'date_of_birth' => '1990-01-01',
                'expiry_date' => '2030-01-01',
                'issuing_authority' => 'هيئة الأوراق الرسمية',
            ],
            'security_features' => [
                'hologram_detected' => true,
                'microprint_detected' => true,
                'uv_features_detected' => true,
                'tampering_detected' => false
            ],
            'flags' => []
        ];
        
        // Simular posibles problemas (con baja probabilidad)
        if (rand(1, 10) === 1) {
            $results['is_authentic'] = false;
            $results['flags'][] = 'possible_forgery';
            $results['security_features']['tampering_detected'] = true;
        }
        
        return $results;
    }
    
    /**
     * Verificar que los datos extraídos coincidan con la información proporcionada
     *
     * @param array $frontResults
     * @param array $backResults
     * @param KycVerification $verification
     * @return array
     */
    protected function verifyExtractedData($frontResults, $backResults, $verification)
    {
        $results = [
            'data_match' => true,
            'confidence' => 0.95,
            'mismatches' => [],
            'flags' => []
        ];
        
        // Verificar coincidencia de número de documento
        if (isset($frontResults['extracted_data']['document_number']) && 
            $frontResults['extracted_data']['document_number'] !== $verification->id_number) {
            $results['data_match'] = false;
            $results['mismatches'][] = 'document_number';
            $results['flags'][] = 'id_number_mismatch';
        }
        
        // Verificar coincidencia de nombre
        if (isset($frontResults['extracted_data']['full_name']) && 
            !$this->namesMatch($frontResults['extracted_data']['full_name'], $verification->full_name)) {
            $results['data_match'] = false;
            $results['mismatches'][] = 'full_name';
            $results['flags'][] = 'name_mismatch';
        }
        
        // Verificar coincidencia de fecha de nacimiento
        if (isset($frontResults['extracted_data']['date_of_birth']) && 
            $frontResults['extracted_data']['date_of_birth'] !== $verification->date_of_birth->format('Y-m-d')) {
            $results['data_match'] = false;
            $results['mismatches'][] = 'date_of_birth';
            $results['flags'][] = 'dob_mismatch';
        }
        
        return $results;
    }
    
    /**
     * Comparar nombres teniendo en cuenta posibles variaciones en el orden
     *
     * @param string $name1
     * @param string $name2
     * @return bool
     */
    protected function namesMatch($name1, $name2)
    {
        // Implementación simplificada - en producción se usaría un algoritmo más sofisticado
        // que tenga en cuenta variaciones culturales en nombres, transliteraciones, etc.
        $name1Parts = explode(' ', strtolower($name1));
        $name2Parts = explode(' ', strtolower($name2));
        
        $commonParts = array_intersect($name1Parts, $name2Parts);
        
        // Si al menos el 70% de las partes del nombre coinciden, consideramos que es el mismo nombre
        return count($commonParts) >= min(count($name1Parts), count($name2Parts)) * 0.7;
    }
    
    /**
     * Verificar que la selfie coincide con la foto del documento
     *
     * @param KycVerification $verification
     * @return array
     */
    protected function verifyFaceMatch(KycVerification $verification)
    {
        $results = [
            'success' => true,
            'match' => true,
            'confidence' => 0,
            'flags' => []
        ];
        
        try {
            // Verificar que existen las imágenes necesarias
            if (!$verification->selfie_path || !Storage::disk('private')->exists($verification->selfie_path)) {
                throw new \Exception('Imagen de selfie no encontrada');
            }
            
            if (!$verification->id_front_path || !Storage::disk('private')->exists($verification->id_front_path)) {
                throw new \Exception('Imagen frontal del documento no encontrada');
            }
            
            // En producción, aquí se conectaría con un servicio de comparación facial
            // como Amazon Rekognition, Azure Face API, etc.
            
            // Simulación de resultados para desarrollo
            $confidence = rand(75, 99) / 100;
            $results['confidence'] = $confidence;
            
            // Si la confianza es baja, marcar como no coincidente
            if ($confidence < 0.80) {
                $results['match'] = false;
                $results['flags'][] = 'low_face_match_confidence';
            }
            
        } catch (\Exception $e) {
            Log::error('Error en la verificación facial: ' . $e->getMessage());
            $results['success'] = false;
            $results['error'] = $e->getMessage();
            $results['flags'][] = 'face_verification_error';
        }
        
        return $results;
    }
    
    /**
     * Detectar si la selfie es de una persona real y no una foto de una foto
     *
     * @param KycVerification $verification
     * @return array
     */
    protected function detectLiveness(KycVerification $verification)
    {
        $results = [
            'success' => true,
            'is_live' => true,
            'confidence' => 0,
            'flags' => []
        ];
        
        try {
            // Verificar que existe la imagen de selfie
            if (!$verification->selfie_path || !Storage::disk('private')->exists($verification->selfie_path)) {
                throw new \Exception('Imagen de selfie no encontrada');
            }
            
            // En producción, aquí se conectaría con un servicio de detección de vida
            // como iProov, FaceTec, etc.
            
            // Simulación de resultados para desarrollo
            $confidence = rand(85, 99) / 100;
            $results['confidence'] = $confidence;
            
            // Simular posibles problemas (con baja probabilidad)
            if (rand(1, 20) === 1) {
                $results['is_live'] = false;
                $results['flags'][] = 'possible_spoofing_attempt';
            }
            
        } catch (\Exception $e) {
            Log::error('Error en la detección de vida: ' . $e->getMessage());
            $results['success'] = false;
            $results['error'] = $e->getMessage();
            $results['flags'][] = 'liveness_detection_error';
        }
        
        return $results;
    }
    
    /**
     * Evaluar el riesgo general del usuario
     *
     * @param KycVerification $verification
     * @return array
     */
    protected function assessRisk(KycVerification $verification)
    {
        $results = [
            'success' => true,
            'risk_level' => 'low',
            'risk_score' => 0,
            'checks' => [],
            'flags' => []
        ];
        
        try {
            // En producción, aquí se conectaría con servicios de evaluación de riesgo
            // como ComplyAdvantage, Trulioo, etc.
            
            // Simulación de verificaciones para desarrollo
            $checks = [
                'sanctions_screening' => [
                    'passed' => true,
                    'matches' => []
                ],
                'pep_screening' => [
                    'passed' => true,
                    'matches' => []
                ],
                'adverse_media' => [
                    'passed' => true,
                    'matches' => []
                ],
                'fraud_database' => [
                    'passed' => true,
                    'matches' => []
                ]
            ];
            
            $results['checks'] = $checks;
            
            // Calcular puntuación de riesgo (0-100, donde 0 es el menor riesgo)
            $riskScore = rand(0, 30);
            $results['risk_score'] = $riskScore;
            
            // Determinar nivel de riesgo
            if ($riskScore < 10) {
                $results['risk_level'] = 'low';
            } elseif ($riskScore < 50) {
                $results['risk_level'] = 'medium';
            } else {
                $results['risk_level'] = 'high';
                $results['flags'][] = 'high_risk_score';
            }
            
            // Simular coincidencias en listas de sanciones (con muy baja probabilidad)
            if (rand(1, 100) === 1) {
                $results['checks']['sanctions_screening']['passed'] = false;
                $results['checks']['sanctions_screening']['matches'][] = [
                    'list' => 'UN Sanctions',
                    'name' => $verification->full_name,
                    'match_score' => 0.85
                ];
                $results['risk_level'] = 'high';
                $results['flags'][] = 'sanctions_match';
            }
            
        } catch (\Exception $e) {
            Log::error('Error en la evaluación de riesgo: ' . $e->getMessage());
            $results['success'] = false;
            $results['error'] = $e->getMessage();
        }
        
        return $results;
    }
    
    /**
     * Calcular la puntuación general del documento
     *
     * @param array $frontResults
     * @param array $backResults
     * @param array $dataResults
     * @return float
     */
    protected function calculateDocumentScore($frontResults, $backResults, $dataResults)
    {
        // Ponderación de cada componente
        $weights = [
            'front_authenticity' => 0.3,
            'back_authenticity' => 0.2,
            'data_match' => 0.5
        ];
        
        // Calcular puntuación ponderada
        $score = 0;
        
        if (isset($frontResults['confidence'])) {
            $score += $frontResults['confidence'] * $weights['front_authenticity'];
        }
        
        if (isset($backResults['confidence'])) {
            $score += $backResults['confidence'] * $weights['back_authenticity'];
        }
        
        if (isset($dataResults['confidence'])) {
            $score += $dataResults['confidence'] * $weights['data_match'];
        }
        
        return $score;
    }
    
    /**
     * Calcular la puntuación general y la recomendación
     *
     * @param array $results
     * @return array
     */
    protected function calculateOverallScore($results)
    {
        // Ponderación de cada componente
        $weights = [
            'document_verification' => 0.4,
            'face_verification' => 0.3,
            'liveness_detection' => 0.2,
            'risk_assessment' => 0.1
        ];
        
        // Calcular puntuación ponderada
        $score = 0;
        
        if (isset($results['document_verification']['score'])) {
            $score += $results['document_verification']['score'] * $weights['document_verification'];
        }
        
        if (isset($results['face_verification']['confidence'])) {
            $score += $results['face_verification']['confidence'] * $weights['face_verification'];
        }
        
        if (isset($results['liveness_detection']['confidence'])) {
            $score += $results['liveness_detection']['confidence'] * $weights['liveness_detection'];
        }
        
        // Para el riesgo, convertimos la puntuación de riesgo (donde menor es mejor) a una escala donde mayor es mejor
        if (isset($results['risk_assessment']['risk_score'])) {
            $riskScore = 1 - ($results['risk_assessment']['risk_score'] / 100);
            $score += $riskScore * $weights['risk_assessment'];
        }
        
        $results['verification_score'] = $score;
        
        // Determinar recomendación
        if ($score >= 0.85) {
            $results['recommendation'] = 'approve';
        } elseif ($score >= 0.70) {
            $results['recommendation'] = 'manual_review';
        } else {
            $results['recommendation'] = 'reject';
        }
        
        // Agregar todas las banderas
        $allFlags = [];
        foreach (['document_verification', 'face_verification', 'liveness_detection', 'risk_assessment'] as $component) {
            if (isset($results[$component]['flags'])) {
                $allFlags = array_merge($allFlags, $results[$component]['flags']);
            }
        }
        $results['flags'] = array_unique($allFlags);
        
        return $results;
    }
    
    /**
     * Guardar los resultados de la verificación
     *
     * @param KycVerification $verification
     * @param array $results
     * @return void
     */
    protected function saveResults(KycVerification $verification, $results)
    {
        // Crear o actualizar el documento KYC
        $document = KycDocument::updateOrCreate(
            ['user_id' => $verification->user_id],
            [
                'document_type' => $verification->id_type,
                'document_number' => $verification->id_number,
                'document_file_path' => $verification->id_front_path,
                'selfie_file_path' => $verification->selfie_path,
                'status' => 'pending',
                'ai_verification_results' => $results
            ]
        );
        
        // Si la recomendación es aprobar automáticamente y la configuración lo permite
        if ($results['recommendation'] === 'approve' && config('kyc.auto_approve_enabled', false)) {
            $document->status = 'approved';
            $document->verified_at = now();
            $document->verified_by = 'ai_system';
            $document->save();
            
            // Actualizar el estado del usuario
            $user = User::find($verification->user_id);
            $user->kyc_verified = true;
            $user->kyc_verified_at = now();
            $user->save();
            
            // Actualizar la verificación
            $verification->status = 'approved';
            $verification->verified_at = now();
            $verification->save();
        }
    }
}
