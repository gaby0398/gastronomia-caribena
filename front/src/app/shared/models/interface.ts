export interface TypeAdmin {
    idUsuario: string,
    nombre: string,
    correo: string,
    direccion: string
}

export enum Role {
    Admin = 1,
    usuario = 2,
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

export interface User {
    idUsuario: string,
    passw: string,
    rol: number
}
export interface Token {
    token: string,
    tkRef: string
}