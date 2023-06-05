export interface StateInputProps {
	className?: string;
	label: string;
	id: string;
	autoComplete?: string;
	value: string;
	country: string;
	onChange: ( value: string ) => void;
	required?: boolean;
	errorMessage?: string | undefined;
	errorId?: string;
}

export type StateInputWithStatesProps = StateInputProps & {
	states: Record< string, Record< string, string > >;
};
