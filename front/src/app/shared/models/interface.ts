export interface TypeClient {
    alias: string,
    nombre: string,
    apellido1: string,
    apellido2: string,
    celular: string,
    correo: string,
    rol: Role;
};

export interface TypeClientV2 {
    alias: string,
    nombre: string,
    apellido1: string,
    apellido2: string,
    telefono: string,
    celular: string,
    correo: string,
    rol: Role,
    passw: string;
};

export interface TypeUser {
    id: number,
    alias: string,
    correo: string,
    rol: number
};

export enum Role {
    administrador = 1,
    supervisor = 2,
    cliente = 3
}

enum RolePath {
    administrador = 'administrador',
    supervisor = 'supervisor',
    cliente = 'cliente'
}

export interface Token {
    token: string,
    tkRef: string
}

export interface IPassw {
    passw: string,
    passwN: string
} 

export class User {
    alias: string;
    nombre: string;
    rol: number;
    constructor(usr?: User) {
        this.alias = usr !== undefined ? usr.alias : '';
        this.nombre = usr !== undefined ? usr.nombre : '';
        this.rol = usr !== undefined ? usr.rol : -1;
    }
}