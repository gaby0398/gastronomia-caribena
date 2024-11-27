export interface TypeAdmin {
    nombre: string,
    correo: string,
    direccion: string
}

export interface TypeClient {
    alias: string,
    nombre: string,
    apellido1: string,
    apellido2: string,
    celular: string,
    correo: string,
    rol: Role;
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
    idUsuario: string;
    nombre: string;
    rol: number;
    constructor(usr?: User) {
        this.idUsuario = usr !== undefined ? usr.idUsuario : '';
        this.nombre = usr !== undefined ? usr.nombre : '';
        this.rol = usr !== undefined ? usr.rol : -1;
    }
}