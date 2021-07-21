export class ModalError extends Error {
    constructor(message) {
        super(message); // (1)
        this.name = "ModalError"; // (2)
    }
}
