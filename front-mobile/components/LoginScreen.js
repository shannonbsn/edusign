import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert } from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const LoginScreen = ({ navigation }) => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    const handleLogin = async () => {
        try {
            const response = await axios.post('http://localhost:8000/api/login', {
                email,
                password
            });

            // Stocker le token et le rôle
            await AsyncStorage.setItem('authToken', response.data.token);
            await AsyncStorage.setItem('userRole', response.data.role);

            if (response.data.role === 'etudiant') {
                navigation.navigate('ScanQrScreen'); // Rediriger les étudiants vers le scan QR
            } else {
                Alert.alert("Accès refusé", "Seuls les étudiants peuvent utiliser cette application.");
            }
        } catch (error) {
            Alert.alert("Erreur", "Identifiants incorrects.");
        }
    };

    return (
        <View style={{ padding: 20 }}>
            <Text style={{ fontSize: 24, textAlign: 'center' }}>Connexion</Text>
            <TextInput
                placeholder="Email"
                value={email}
                onChangeText={setEmail}
                style={{ borderBottomWidth: 1, marginBottom: 10 }}
            />
            <TextInput
                placeholder="Mot de passe"
                value={password}
                onChangeText={setPassword}
                secureTextEntry
                style={{ borderBottomWidth: 1, marginBottom: 10 }}
            />
            <TouchableOpacity onPress={handleLogin} style={{ backgroundColor: '#4CAF50', padding: 10, alignItems: 'center' }}>
                <Text style={{ color: 'white' }}>Se connecter</Text>
            </TouchableOpacity>
        </View>
    );
};

export default LoginScreen;