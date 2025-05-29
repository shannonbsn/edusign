import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert } from 'react-native';
import axios from 'axios';

const RegisterScreen = ({ navigation }) => {
    const [nom, setNom] = useState('');
    const [prenom, setPrenom] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [role, setRole] = useState('etudiant');

    const handleRegister = async () => {
        try {
            await axios.post('http://localhost:8000/api/register', {
                nom,
                prenom,
                email,
                password,
                role
            });

            Alert.alert("Succès", "Inscription réussie !");
            navigation.navigate('LoginScreen');
        } catch (error) {
            Alert.alert("Erreur", "Inscription échouée.");
        }
    };

    return (
        <View style={{ padding: 20 }}>
            <Text style={{ fontSize: 24, textAlign: 'center' }}>Inscription</Text>
            <TextInput placeholder="Nom" value={nom} onChangeText={setNom} style={{ borderBottomWidth: 1, marginBottom: 10 }} />
            <TextInput placeholder="Prénom" value={prenom} onChangeText={setPrenom} style={{ borderBottomWidth: 1, marginBottom: 10 }} />
            <TextInput placeholder="Email" value={email} onChangeText={setEmail} style={{ borderBottomWidth: 1, marginBottom: 10 }} />
            <TextInput placeholder="Mot de passe" value={password} onChangeText={setPassword} secureTextEntry style={{ borderBottomWidth: 1, marginBottom: 10 }} />
            <TouchableOpacity onPress={handleRegister} style={{ backgroundColor: '#007BFF', padding: 10, alignItems: 'center' }}>
                <Text style={{ color: 'white' }}>S'inscrire</Text>
            </TouchableOpacity>
        </View>
    );
};

export default RegisterScreen;