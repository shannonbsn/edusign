import React, { useState, useEffect } from 'react';
import { View, Text, Button, Alert } from 'react-native';
import { BarCodeScanner } from 'expo-barcode-scanner';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const ScanQrScreen = ({ navigation }) => {
    const [hasPermission, setHasPermission] = useState(null);
    const [scanned, setScanned] = useState(false);

    useEffect(() => {
        const getCameraPermissions = async () => {
            const { status } = await BarCodeScanner.requestPermissionsAsync();
            setHasPermission(status === 'granted');
        };
        getCameraPermissions();
    }, []);

    const handleBarCodeScanned = async ({ data }) => {
        setScanned(true);

        try {
            const token = await AsyncStorage.getItem('authToken');
            const response = await axios.post(`http://localhost:8000/api/scan-presence/${data}`, {}, {
                headers: { Authorization: `Bearer ${token}` }
            });

            Alert.alert("Présence enregistrée ✅", response.data.message);
        } catch (error) {
            Alert.alert("Erreur ❌", "QR Code invalide ou expiré.");
        }
    };

    if (hasPermission === null) {
        return <Text>📷 Demande de permission en cours...</Text>;
    }
    if (hasPermission === false) {
        return <Text>❌ Accès refusé à l'appareil photo.</Text>;
    }

    return (
        <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
            <BarCodeScanner
                onBarCodeScanned={scanned ? undefined : handleBarCodeScanned}
                style={{ width: '100%', height: '80%' }}
            />
            {scanned && <Button title="🔄 Re-scanner" onPress={() => setScanned(false)} />}
        </View>
    );
};

export default ScanQrScreen;