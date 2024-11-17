export interface TypeAdmin {
    idUsuario: string,
    nombre: string,
    correo: string,
    direccion: string
}

export interface TypeClient {
    id: number,
    idUsuario: string,
    nombre: string,
    apellido1: string,
    apellido2: string,
    telefono: string,
    celular: string,
    direccion: string,
    correo: string,
    fechaIngreso: string
};

export interface TypeUser {
    idUsuario: string,
    alias: string,
    correo: string,
    rol: number
};

export enum Role {
    administrador = 1,
    supervisor = 2,
    cliente = 4
}

/*export interface User {
    idUsuario: string,
    passw: string,
    rol: number
}*/
export interface Token {
    token: string,
    tkRef: string
}

export interface IPassw {
    passw: string,
    passwN: string
} 

export interface IUser {
    usuario: string,
    nombre: string,
    rol: number
};

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